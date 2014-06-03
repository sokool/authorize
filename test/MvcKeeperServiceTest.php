<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 03/06/14
 * Time: 15:19
 */

namespace AuthorizeTest;

use Authorize\Factory\MvcKeeperFactory;
use Authorize\Service\MvcKeeper;
use AuthorizeTest\Controller\TestController;
use FloTest\Bootstrap;

class MvcKeeperServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testA()
    {
    }
//    /**
//     * @var MvcKeeper
//     */
//    protected $service;
//
//    public function setUp()
//    {
//        Bootstrap::getServiceManager()
//            ->setAllowOverride(true)
//            ->setFactory('Authorize\Provider\Role', 'AuthorizeTest\Asset\Provider\Role')
//            ->setFactory('Authorize\Provider\Permission', 'AuthorizeTest\Asset\Provider\Permission');
////            ->setFactory('Zend\Authentication\AuthenticationService', function ($sm) {
////                echo 'shite';
////                //exit;
////            });
//
//        $this->service = (new MvcKeeperFactory())->createService(Bootstrap::getServiceManager());
//    }
//
//    public function testDefaultInstances()
//    {
//
//        $this->assertInstanceOf('Authorize\Service\RbacService', $this->service->getRbac());
//        //$this->assertInstanceOf('Authorize\Provider\Role\RoleProviderInterface', $this->service->getIdentity());
//    }
//
//    public function testMy()
//    {
//        $annotatedObject = new TestController();
//        $this->service->hasAccess(get_class($annotatedObject), 'someSpecificCustomAction');
//
//    }
}