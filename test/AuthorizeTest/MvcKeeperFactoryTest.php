<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 03/06/14
 * Time: 14:36
 */

namespace AuthorizeTest;

use AuthorizeTest\Asset\Authentication\FakeAdapter;
use MintSoft\Authorize\Factory\MvcKeeperFactory;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\NonPersistent;

class MvcKeeperFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MvcKeeperFactory
     */
    protected $factory;

    public function setUp()
    {
        \Bootstrap::getServiceManager()
            ->setAllowOverride(true)
            ->setFactory('Zend\Authentication\AuthenticationService', function () {
                FakeAdapter::$userIdentity = 'dood@somewhere.de';

                $authenticationService = new AuthenticationService(new NonPersistent(), new FakeAdapter);
                $authenticationService->authenticate();

                return $authenticationService;
            });


        $this->factory = new MvcKeeperFactory();
    }

    public function testInstanceReturned()
    {
        $this->assertInstanceOf(
            'MintSoft\Authorize\Service\MvcKeeper',
            $this->factory->createService(\Bootstrap::getServiceManager())
        );
    }
} 