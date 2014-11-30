<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 27.11.14
 * Time: 14:36
 */

namespace MintSoft\Authorize\Mvc;

use MintSoft\Authorize\Authorize;
use Nette\Diagnostics\Debugger;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\View\Model\ViewModel;

class AuthorizeListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'checkControllerAccess'));
    }

    /**
     * MVC event, this method is fired when route is performed. Method will check,
     * if current identity has access to matched MVC Route. Access will be checked
     * based on identity roles and permissions.
     *
     * In case if identity has no access to the resources, then 403 HTTP status is set in Response object.
     *
     * @param MvcEvent $mvcEvent
     */
    public function checkControllerAccess(MvcEvent $mvcEvent)
    {
        /** @var ControllerGuard $controllerGuard */
        /** @var Authorize $authorize */
        $serviceManager  = $mvcEvent->getApplication()->getServiceManager();
        $controllerGuard = $serviceManager->get('MintSoft\Authorize\ControllerGuard');
        $identity        = 'a';
        try {
            $identity = $serviceManager->get('Zend\Authentication\AuthenticationService')->getIdentity();
        } catch (ServiceNotFoundException $notFound) {
        }

        // If access is denied, then set Http response as 403 - AccessForbidden
        if (!$controllerGuard->hasAccess($mvcEvent->getRouteMatch(), $identity)) {
            $mvcEvent->getResponse()->setStatusCode(Response::STATUS_CODE_403);
            $mvcEvent
                ->setError('Not allowed')
                ->setViewModel(
                    (new ViewModel())
                        ->setTemplate('error/403'));
        }
    }
}
