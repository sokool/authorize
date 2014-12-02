<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 30.11.14
 * Time: 10:23
 */

namespace MintSoft\Authorize\Mvc\Controller\Plugin;

use MintSoft\Authorize\Mvc\ControllerGuard;
use Zend\Mvc\Controller\Plugin\Identity as ZendIdentity;
use Zend\Mvc\Router\SimpleRouteStack;
use Zend\Http\Request as HttpRequest;

class User extends ZendIdentity
{
    /**
     * @var ControllerGuard
     */
    protected $controllerGuard;

    /**
     * @var SimpleRouteStack
     */
    protected $router;

    protected $identity = null;

    public function __construct(ControllerGuard $controllerGuard)
    {
        $this->controllerGuard = $controllerGuard;
    }

    /**
     * @return mixed
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @param mixed $identity
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
    }

    /**
     * @param SimpleRouteStack $routeStack
     *
     * @return $this
     */
    public function setRouter(SimpleRouteStack $routeStack)
    {
        $this->router = $routeStack;

        return $this;
    }

    /**
     * @return SimpleRouteStack
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
            $this->identity = $this->getIdentity();
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