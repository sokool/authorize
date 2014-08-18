<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 19/05/14
 * Time: 14:44
 */

namespace MintSoft\Authorize\View\Helper;

use MintSoft\Authorize\Service\MvcKeeper;
use Zend\Http\Request as HttpRequest;
use Zend\Mvc\Controller\ControllerManager;
use Zend\Mvc\Router\SimpleRouteStack;
use Zend\View\Helper\AbstractHelper;

/**
 * Class User
 *
 * @todo    refactor this class, Services need to be injected into that class, not called from ServiceLocator
 * @package Authorize\View\Helper
 */
class User extends AbstractHelper
{
    /**
     * @var MvcKeeper
     */
    protected $mvcKeeper;

    /**
     * @var ControllerManager
     */
    protected $controllerManager;

    /**
     * @var SimpleRouteStack
     */
    protected $routeStack;

    public function setMvcKeeper(MvcKeeper $mvcKeeper)
    {
        $this->mvcKeeper = $mvcKeeper;
    }

    /**
     * @return MvcKeeper
     */
    public function getMvcKeeper()
    {
        return $this->mvcKeeper;
    }

    /**
     * @param \Zend\Mvc\Controller\ControllerManager $controllerManager
     */
    public function setControllerManager($controllerManager)
    {
        $this->controllerManager = $controllerManager;
    }

    /**
     * @return \Zend\Mvc\Controller\ControllerManager
     */
    public function getControllerManager()
    {
        return $this->controllerManager;
    }

    /**
     * @param \Zend\Mvc\Router\SimpleRouteStack $routeStack
     */
    public function setRouteStack($routeStack)
    {
        $this->routeStack = $routeStack;
    }

    /**
     * @return \Zend\Mvc\Router\SimpleRouteStack
     */
    public function getRouteStack()
    {
        return $this->routeStack;
    }

    /**
     * @return mixed
     */
    public function identity()
    {
        return $this->mvcKeeper->getAuthenticationService()->getIdentity();
    }

    /**
     * @param $roleName
     *
     * @return bool
     */
    public function isInRole($roleName)
    {
        $identity    = $this->identity();
        $rbacService = $this->mvcKeeper->getRbac();

        return $rbacService->hasRole($identity, $roleName);
    }

    /**
     * Check if logged user has access to selected resource
     *
     * @param  string $permission Resource
     *
     * @return bool Has permission?
     */
    public function hasPermission($permission)
    {
        $identity    = $this->identity();
        $rbacService = $this->mvcKeeper->getRbac();

        return $rbacService->hasPermission($identity, $permission);
    }

    /**
     * Check if current user can call controller action
     *
     * @param $routeName Route name to module, controller, action.
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function hasAccess($routeName)
    {
        $routerService     = $this->getRouteStack();
        $controllerManager = $this->getControllerManager();
        $mvcKeeper         = $this->getMvcKeeper();
        $route             = $routerService->getRoute($routeName);

        if (null == $route) {
            throw new \Exception;
        }

        $routeMatch = $routerService->match(
            (new HttpRequest())
                ->setUri($route->assemble())
        );

        $controllerClass  = get_class($controllerManager->get($routeMatch->getParam('controller', null)));
        $controllerMethod = $routeMatch->getParam('action', null) . 'Action';

        return $mvcKeeper->hasAccess($controllerClass, $controllerMethod);
    }
}