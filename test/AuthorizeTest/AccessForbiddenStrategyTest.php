<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 20/06/14
 * Time: 13:15
 */

namespace AuthorizeTest;

use MintSoft\Authorize\View\AccessForbiddenStrategy;
use Zend\EventManager\EventManager;
use Zend\Http\PhpEnvironment\Response as HttpResponse;
use Zend\Mvc\MvcEvent;

class AccessForbiddenStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testListenerAgregateInstance()
    {
        $this->assertInstanceOf('Zend\EventManager\ListenerAggregateInterface', new AccessForbiddenStrategy());
    }

    public function testEvent()
    {
        $eventManager = new EventManager();
        $eventManager->attach(new AccessForbiddenStrategy());

        $this->assertEquals($eventManager->getListeners(MvcEvent::EVENT_ROUTE)->count(), 1);
    }

    public function testResponse()
    {
        $strategy = new AccessForbiddenStrategy();
        $mvcEvent = new MvcEvent();
        $response = new HttpResponse();

        //HTTP response with 200 status!
        $response->setStatusCode(HttpResponse::STATUS_CODE_200);
        $mvcEvent->setResponse($response);

        //Assertions - no acction from AccessForbiddenStrategy
        $strategy->onHttpForbidden($mvcEvent);
        $this->assertEquals($mvcEvent->getError(), '');
        $this->assertEquals($mvcEvent->getViewModel()->getTemplate(), '');

        // HTTP response with forbidden status !
        $response->setStatusCode(HttpResponse::STATUS_CODE_403);
        $mvcEvent->setResponse($response);

        //Assertions - event has error string and ViewModel to render AccessForbidden page.
        $strategy->onHttpForbidden($mvcEvent);
        $this->assertEquals($mvcEvent->getError(), 'error/403');
        $this->assertEquals($mvcEvent->getViewModel()->getTemplate(), 'error/403');
    }
}