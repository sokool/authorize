<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 30.11.14
 * Time: 10:23
 */

namespace MintSoft\Authorize\Mvc\Controller\Plugin;

use MintSoft\Authorize\Mvc\ControllerGuard;
use Nette\Diagnostics\Debugger;
use Zend\Mvc\Controller\Plugin\Identity as ZendIdentity;
use Zend\Mvc\Router\SimpleRouteStack;
use Zend\Http\Request as HttpRequest;

class Identity extends ZendIdentity
{
    /**
     * @var ControllerGuard
     */
    protected $controllerGuard;

    protected $router;

    protected $identity;

    public function __construct(ControllerGuard $controllerGuard)
    {
        $this->controllerGuard = $controllerGuard;
    }

    /**
     * @param \Zend\Mvc\Router\SimpleRouteStack $routeStack
     *
     * @return $this
     */
    public function setRouter(SimpleRouteStack $routeStack)
    {
        $this->router = $routeStack;

        return $this;
    }

    /**
     * @return \Zend\Mvc\Router\SimpleRouteStack
     */
    protected function getRouter()
    {
        return $this->router;
    }

    /**
     * @return ControllerGuard
     */
    protected function getControllerGuard()
    {
        return $this->controllerGuard;
    }

    public function __invoke($identity = null)
    {
        if (is_null($identity)) {
            return parent::__invoke();
        }

        $this->identity = $identity;

        return $this;
    }

    public function inRole($roleName, $permissions = null)
    {
        $authorize = $this
            ->getControllerGuard()
            ->getAuthorize();

        return $permissions ?
            $authorize->isGranted($this->identity, $roleName, $permissions) :
            $authorize->hasRole($this->identity, $roleName);
    }

    public function hasAccess($routeName)
    {
        $router = $this->getRouter();
        $guard  = $this->getControllerGuard();
        $route  = $router->getRoute($routeName);

        if (null == $route) {
            throw new \Exception('Route (' . $routeName . ') not exist');
        }

        $routeMatch = $router->match(
            (new HttpRequest())
                ->setUri($route->assemble())
        );

        return $guard->hasAccess($routeMatch, $this->identity);
    }
}