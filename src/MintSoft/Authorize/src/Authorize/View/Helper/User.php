<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 19/05/14
 * Time: 14:44
 */

namespace MintSoft\Authorize\View\Helper;

use MintSoft\Authorize\Service\RbacService;
use Zend\Http\Request as HttpRequest;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;

class User extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $serviceManager;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceManager = $serviceLocator->getServiceLocator();
    }

    public function getServiceLocator()
    {
        return $this->serviceManager;
    }

    /**
     * @return mixed
     */
    public function identity()
    {
        return $this->getView()->identity();
    }

    /**
     * @param $roleName
     *
     * @return bool
     */
    public function isInRole($roleName)
    {
        /** @var $rbacService RbacService */
        $rbacService = $this->getServiceLocator()->get('MintSoft\Authorize\Rbac');

        return $rbacService->hasRole($this->identity(), $roleName);
    }

    public function hasPermission($permission)
    {
        throw new \Exception('Not implemented yet!');
    }

    /**
     * Check if current user can call controller action
     *
     * @param $routeName Route name to module, controller, action.
     *
     * @throws \Exception
     */
    public function hasAccess($routeName)
    {
        $routerService     = $this->getServiceLocator()->get('Router');
        $controllerManager = $this->getServiceLocator()->get('ControllerManager');
        $mvcKeeper         = $this->getServiceLocator()->get('MintSoft\Authorize\MvcKeeper');
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