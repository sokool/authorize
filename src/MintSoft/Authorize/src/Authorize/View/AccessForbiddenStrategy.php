<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 19/05/14
 * Time: 08:46
 */

namespace MintSoft\Authorize\View;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class AccessForbiddenStrategy implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    public function attach(EventManagerInterface $events)
    {
        // Lower priority for this is necessary, MvcKeeper is on the same event route, view strategy need to has lower priority.
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onHttpForbidden'), -1);
    }

    public function onHttpForbidden(MvcEvent $mvcEvent)
    {
        $errorTemplateName = 'error/403';
        $httpResponse      = $mvcEvent->getResponse();
        if (!$httpResponse instanceof HttpResponse || $httpResponse->getStatusCode() !== 403) {
            return;
        }

        //Tell MVC event about errors and push AccessForbidden model to render.
        $mvcEvent
            ->setError($errorTemplateName)
            ->setViewModel(
                (new ViewModel())
                    ->setTemplate($errorTemplateName));
    }
}