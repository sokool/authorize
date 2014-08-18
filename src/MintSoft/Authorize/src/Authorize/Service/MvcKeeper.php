<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 15/05/14
 * Time: 13:20
 */

namespace MintSoft\Authorize\Service;

use MintSoft\Authorize\Annotation\AnnotationBuilder as AuthorizeBuilder;
use MintSoft\Authorize\Annotation\Authorize;
use Zend\Authentication\AuthenticationService;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\MvcEvent;

class MvcKeeper implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var AuthorizeBuilder
     */
    protected $authorizeBuilder;

    /**
     * @var RbacService
     */
    protected $rbacService;

    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @param RbacService           $rbacService
     * @param AuthenticationService $authenticationService
     * @param AuthorizeBuilder      $authorizeBuilder
     */
    public function __construct(RbacService $rbacService, AuthenticationService $authenticationService, AuthorizeBuilder $authorizeBuilder)
    {
        $this->rbacService           = $rbacService;
        $this->authenticationService = $authenticationService;
        $this->authorizeBuilder      = $authorizeBuilder;
    }

    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'));
    }

    /**
     * @return AuthorizeBuilder
     */
    public function getAuthorizeBuilder()
    {
        return $this->authorizeBuilder;
    }

    /**
     * @return \Zend\Authentication\AuthenticationService
     */
    public function getAuthenticationService()
    {
        return $this->authenticationService;
    }

    /**
     * Gives currently logged user.
     *
     * @return mixed|null
     */
    public function getIdentity()
    {
        return $this->authenticationService->getIdentity();
    }

    /**
     * Take RBAC object - which manages identity roles and permissions, globally.
     *
     * @return RbacService
     */
    public function getRbac()
    {
        return $this->rbacService;
    }

    /**
     * Check if for given Authorize annotation, access is allowed for current identity,
     * it's roles and permissions.
     *
     * @param Authorize $annotation
     *
     * @return bool TRUE if authorization is allowed, otherwise FALSE
     */
    private function isAllowed(Authorize $annotation)
    {
        $rbac     = $this->getRbac();
        $identity = $this->getIdentity();

        if (null === $identity) {
            return false;
        }

        if ($annotation->isEmpty()) {
            return true;
        }

        // Checking all the annotation resources
        $allowed = false;
        foreach ($annotation->getRoles() as $role) {
            $allowed = $rbac->hasRole($identity, $role) || $allowed;
            if ($allowed) {
                return true;
            }
        }

        foreach ($annotation->getPermissions() as $permission) {
            $allowed = $rbac->hasPermission($identity, $permission) || $allowed;
            if ($allowed) {
                return true;
            }
        }

        /**
         * @todo add implementations for user level access
         */

        return $allowed;
    }

    /**
     * MVC event, this method is fired when route is performed. Method will check,
     * if current identity has access to requested [Controller, Action] based on his
     * roles and permissions.
     *
     * In case if identity has no access to the resources, then 403 HTTP status is set in Response object.
     *
     * @param MvcEvent $mvcEvent
     */
    public function onRoute(MvcEvent $mvcEvent)
    {
        $routeMatch       = $mvcEvent->getRouteMatch();
        $controllerClass  = get_class($mvcEvent->getApplication()->getServiceManager()->get('ControllerManager')->get($routeMatch->getParam('controller', null)));
        $controllerMethod = $routeMatch->getParam('action', null) . 'Action';

        // If access is denied, then set Http response as 403 - AccessForbidden
        if (!$this->hasAccess($controllerClass, $controllerMethod)) {
            $mvcEvent->getResponse()->setStatusCode(403);
        }
    }

    /**
     * @param $className
     * @param $methodName
     *
     * @return bool
     */
    public function hasAccess($className, $methodName)
    {
        $authorizeConfig     = $this->authorizeBuilder->getAuthorizeConfig();
        $controllerAuthorize = $authorizeConfig[$className];

        // Check access based on collected annotations.
        // Checking access on class level.
        $accessAllowed = true;
        if ($controllerAuthorize['class']) {
            $accessAllowed = $this->isAllowed($controllerAuthorize['class']) && $accessAllowed;
        }

        // Checking access on method level.
        if ($accessAllowed) {
            $authorizeAnnotation = array_key_exists($methodName, $controllerAuthorize['methods']) ? $controllerAuthorize['methods'][$methodName] : null;
            if ($authorizeAnnotation) {
                $accessAllowed = $this->isAllowed($authorizeAnnotation) && $accessAllowed;
            }
        }

        return $accessAllowed;
    }
}