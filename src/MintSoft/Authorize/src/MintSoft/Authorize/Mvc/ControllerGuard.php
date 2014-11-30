<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 29.11.14
 * Time: 13:11
 */

namespace MintSoft\Authorize\Mvc;

use MintSoft\Authorize\Annotation\Authorize as AuthorizeAnnotation;
use MintSoft\Authorize\Annotation\AuthorizeBuilder;
use MintSoft\Authorize\Authorize;
use Zend\Mvc\Controller\ControllerManager;
use Zend\Cache\Storage\Adapter\Memory as MemoryCache;
use Zend\Cache\Storage\Adapter\AbstractAdapter as AbstractCacheStorage;
use Zend\Mvc\Router\RouteMatch;

class ControllerGuard
{
    const CACHE = 'mvc-guard';

    /**
     * @var Authorize
     */
    protected $authorize;

    /**
     * @var AuthorizeBuilder
     */
    protected $authorizeBuilder;

    /**
     * @var ControllerManager
     */
    protected $controllerManager;

    /**
     * @var AbstractCacheStorage
     */
    protected $cacheAdapter;

    /**
     * @param Authorize         $authorize
     * @param ControllerManager $controllerManager
     */
    public function __construct(Authorize $authorize, ControllerManager $controllerManager)
    {
        $this->authorize         = $authorize;
        $this->controllerManager = $controllerManager;
    }

    /**
     * @return ControllerManager
     */
    public function getControllerManager()
    {
        return $this->controllerManager;
    }

    /**
     * @param ControllerManager $controllerManager
     */
    public function setControllerManager(ControllerManager $controllerManager)
    {
        $this->controllerManager = $controllerManager;
    }

    /**
     * @return Authorize
     */
    public function getAuthorize()
    {
        return $this->authorize;
    }

    /**
     * @param Authorize $authorize
     */
    public function setAuthorize(Authorize $authorize)
    {
        $this->authorize = $authorize;
    }

    /**
     * @return AuthorizeBuilder
     */
    public function getAuthorizeBuilder()
    {
        if ($this->authorizeBuilder) {
            return $this->authorizeBuilder;
        }

        $this->setAuthorizeBuilder(new AuthorizeBuilder);

        return $this->authorizeBuilder;
    }

    /**
     * @param AuthorizeBuilder $authorizeBuilder
     *
     * @return $this
     */
    public function setAuthorizeBuilder(AuthorizeBuilder $authorizeBuilder)
    {
        $this->authorizeBuilder = $authorizeBuilder;

        return $this;
    }

    /**
     * @param AbstractCacheStorage $cache
     *
     * @return $this
     */
    public function setCacheAdapter(AbstractCacheStorage $cache)
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
     * NOTE: please note that method will cache results of building annotations.
     *
     * @return array
     */
    private function getControllersAnnotations()
    {
        $cacheAdapter    = $this->getCacheAdapter();
        $authorizeConfig = $cacheAdapter->getItem(self::CACHE);
        if (!empty($authorizeConfig)) {
            $authorizeConfig = unserialize($authorizeConfig);
        } else {
            $authorizeBuilder = $this->getAuthorizeBuilder();
            foreach ($this->getControllerManager()->getCanonicalNames() as $canonical => $controllerName) {
                $controller = $this->getControllerManager()->get($canonical);
                $authorizeBuilder->addClass($controller);
            }
            $authorizeConfig = $authorizeBuilder->buildAnnotations();
            $cacheAdapter->setItem(self::CACHE, serialize($authorizeConfig));
        }

        return $authorizeConfig;
    }

    /**
     * Check if for given Authorize annotation, access is allowed for given identity,
     * it's roles and permissions.
     *
     * @param array $annotations
     *
     * @param null  $identity
     *
     * @return bool TRUE if authorization is allowed, otherwise FALSE
     */
    private function isAllowed(array $annotations, $identity = null)
    {
        if (empty($annotations)) {
            return true;
        }

        $authenticated = array_key_exists('authenticated', $annotations) ? $annotations['authenticated'] : null;
        $roles         = array_key_exists('roles', $annotations) ? $annotations['roles'] : null;
        $lock          = array_key_exists('lock', $annotations) ? $annotations['lock'] : null;

        // Checking all the annotation resources
        if ($authenticated) {
            if ($identity == null) {
                return false;
            }
            $name = $authenticated->getName();

            return empty($name) ? true : $name == $identity;
        }

        foreach ($roles as $role) {
            $roleName = $role->getName();
            if (($permissions = $role->getPermissions())) {
                foreach ($permissions as $permission) {
                    if ($this->authorize->isGranted($identity, $roleName, $permission)) {
                        return true;
                    }
                }
                continue;
            }

            if ($this->authorize->hasRole($identity, $roleName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param RouteMatch $routeMatch
     *
     * @return bool
     */
    public function hasAccess(RouteMatch $routeMatch, $identity = null)
    {
        $controllerName  = $routeMatch->getParam('controller', null);
        $className       = get_class($this->controllerManager->get($controllerName));
        $methodName      = $routeMatch->getParam('action', null) . 'Action';
        $authorizeConfig = $this->getControllersAnnotations();

        if (!array_key_exists($className, $authorizeConfig)) {
            return true;
        }

        // Check access based on collected annotations.
        // Checking access on class level.
        $accessAllowed       = true;
        $controllerAuthorize = $authorizeConfig[$className];
        if (array_key_exists('class', $controllerAuthorize)) {
            $accessAllowed = $this->isAllowed($controllerAuthorize['class'], $identity) && $accessAllowed;
        }

        // Checking access on method level.
        if ($accessAllowed) {
            $authorizeAnnotation = array_key_exists($methodName, $controllerAuthorize['methods']) ? $controllerAuthorize['methods'][$methodName] : null;
            if ($authorizeAnnotation) {
                $accessAllowed = $this->isAllowed($authorizeAnnotation, $identity) && $accessAllowed;
            }
        }

        return $accessAllowed;
    }
}