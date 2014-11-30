<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 20/05/14
 * Time: 09:29
 */

namespace MintSoft\Authorize\Annotation;

use Nette\Diagnostics\Debugger;
use Zend\Code\Annotation\AnnotationCollection;
use Zend\Code\Annotation\AnnotationManager;
use Zend\Code\Annotation\Parser\DoctrineAnnotationParser;
use Zend\Code\Reflection\ClassReflection;

class AuthorizeBuilder
{
    /**
     * @var
     */
    protected $annotationManager;

    /**
     * @var array Default annotations to register
     */
    protected $defaultAnnotations = [
        'MintSoft\Authorize\Annotation\Role',
        'MintSoft\Authorize\Annotation\Lockable',
        'MintSoft\Authorize\Annotation\Authenticated',
    ];

    /**
     * @var array
     */
    protected $classes = [];

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
     * Based on AnnotationCollection, which usually is a product of class reflection,
     * method extract \Authorize\Annotations\Authorize annotation.
     *
     * @param AnnotationCollection $annotationCollection
     *
     * @return Authorize|null
     */
    protected function getAuthorizeAnnotations(AnnotationCollection $annotationCollection)
    {
        $annotations = [];
        foreach ($annotationCollection as $annotation) {
            if ($annotation instanceof Role) {
                $annotations['roles'][] = $annotation;
            } elseif ($annotation instanceof Lockable) {
                $annotations['lock'] = $annotation;
            } elseif ($annotation instanceof Authenticated) {
                $annotations['authenticated'] = $annotation;
            }
        }

        return $annotations;
    }

    /**
     * @param $class
     *
     * @return $this
     */
    public function addClass($class)
    {
        if (!is_object($class) && !class_exists($class)) {
            throw new \InvalidArgumentException('Can not get annotations from class which not exists');
        }

        $this->classes[] = is_object($class) ? get_class($class) : $class;

        return $this;
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
    protected function grab($class)
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
                'class'   => $this->getAuthorizeAnnotations($annotations),
                'methods' => []
            ];
        }

        foreach ($reflection->getMethods() as $method) {
            $methodAnnotations = $method->getAnnotations($manager);
            if (!$methodAnnotations) {
                break;
            }
            $authorizeAnnotation = $this->getAuthorizeAnnotations($methodAnnotations);
            if ($authorizeAnnotation) {
                $configuration['methods'][$method->getName()] = $authorizeAnnotation;
            }
        }

        return $configuration;
    }

    public function buildAnnotations()
    {
        $authorizeSpecs = [];
        foreach ($this->classes as $className) {
            $annotations = $this->grab($className);
            if (empty($annotations)) {
                continue;
            }
            $authorizeSpecs[$className] = $annotations;
        }

        return $authorizeSpecs;
    }
}