<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 20/05/14
 * Time: 09:29
 */

namespace MintSoft\Authorize\Annotation;

use Zend\Cache\Storage\Adapter\AbstractAdapter as CacheAdapter;
use Zend\Cache\Storage\Adapter\Memory as MemoryCache;
use Zend\Code\Annotation\AnnotationCollection;
use Zend\Code\Annotation\AnnotationManager;
use Zend\Code\Annotation\Parser\DoctrineAnnotationParser;
use Zend\Code\Reflection\ClassReflection;
use Zend\Mvc\Controller\ControllerManager;

class AnnotationBuilder
{
    const CACHE = 'mvc-keeper';

    protected $controllerManager;
    protected $annotationManager;
    protected $cacheAdapter;
    /**
     * @var array Default annotations to register
     */
    protected $defaultAnnotations = [
        'MintSoft\Authorize\Annotation\Authorize',
    ];

    /**
     * @todo this class should not depend on ControllerManager!
     *
     * @param ControllerManager $controllerManager
     */
    public function __construct(ControllerManager $controllerManager)
    {
        $this->controllerManager = $controllerManager;
    }

    /**
     * @param AnnotationManager $annotationManager
     *
     * @return $this
     */
    public function setAnnotationManager(AnnotationManager $annotationManager)
    {
        $parser = new DoctrineAnnotationParser();
        foreach ($this->defaultAnnotations as $annotationClass) {
            $parser->registerAnnotation($annotationClass);
        }
        $annotationManager->attach($parser);
        $this->annotationManager = $annotationManager;

        return $this;
    }

    /**
     * @return AnnotationManager
     */
    public function getAnnotationManager()
    {
        if ($this->annotationManager) {
            return $this->annotationManager;
        }

        $this->setAnnotationManager(new AnnotationManager());

        return $this->annotationManager;
    }

    /**
     * @param CacheAdapter $cache
     *
     * @return $this
     */
    public function setCacheAdapter(CacheAdapter $cache)
    {
        $this->cacheAdapter = $cache;

        return $this;
    }

    /**
     * @return CacheAdapter
     */
    public function getCacheAdapter()
    {
        if (!$this->cacheAdapter) {
            return $this->cacheAdapter = new MemoryCache();
        }

        return $this->cacheAdapter;
    }

    /**
     * Based on AnnotationCollection, which usually is a product of class reflection,
     * method extract \Authorize\Annotations\Authorize annotation.
     *
     * @param AnnotationCollection $annotationCollection
     *
     * @return Authorize|null
     */
    protected function getAuthorize(AnnotationCollection $annotationCollection)
    {
        foreach ($annotationCollection as $annotation) {
            if (!$annotation instanceof Authorize) {
                continue;
            }

            return $annotation;
        }

        return null;
    }

    /**
     * Based on given class name or object, method will extract Authorize annotations form
     * class and it's methods.
     *
     *
     * @param object|string
     *
     * @return array of Authorize annotations.
     * @throws \InvalidArgumentException
     */
    public function buildAnnotations($class)
    {
        if (!is_object($class) && !class_exists($class)) {
            throw new \InvalidArgumentException('Can not get annotations from class which not exists');
        }

        $configuration = [];
        $reflection    = new ClassReflection($class);
        $manager       = $this->getAnnotationManager();
        $annotations   = $reflection->getAnnotations($manager);
        if ($annotations instanceof AnnotationCollection) {
            $configuration = [
                'class'   => $this->getAuthorize($annotations),
                'methods' => []
            ];
        }

        foreach ($reflection->getMethods() as $method) {
            $methodAnnotations = $method->getAnnotations($manager);
            if (!$methodAnnotations) {
                break;
            }
            $authorizeAnnotation = $this->getAuthorize($methodAnnotations);
            if ($authorizeAnnotation) {
                $configuration['methods'][$method->getName()] = $authorizeAnnotation;
            }
        }

        return $configuration;
    }

    /**
     * NOTE: please note that method will cache results of building annotations.
     *
     * @return array
     */
    public function getAuthorizeConfig()
    {
        $controllerManager = $this->controllerManager;
        $cacheAdapter      = $this->getCacheAdapter();
        $authorizeConfig   = $cacheAdapter->getItem(self::CACHE);

        if (!empty($authorizeConfig)) {
            $authorizeConfig = unserialize($authorizeConfig);
        } else {
            foreach ($controllerManager->getCanonicalNames() as $canonical => $controllerName) {
                $controller  = $controllerManager->get($canonical);
                $annotations = $this->buildAnnotations($controller);
                if (empty($annotations)) {
                    continue;
                }
                $authorizeConfig[get_class($controller)] = $annotations;
            }
            $cacheAdapter->setItem(self::CACHE, serialize($authorizeConfig));
        }

        return $authorizeConfig;
    }
} 