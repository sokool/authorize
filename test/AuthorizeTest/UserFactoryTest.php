<?php
/**
 * UserFactoryTest class container
 *
 * @package     AuthorizeTest
 * @copyright   2012 SMT Software S.A.
 */

namespace AuthorizeTest;

use FloTest\Bootstrap;
use MintSoft\Authorize\Factory\UserFactory;

/**
 * UserFactory tests
 *
 * @package AuthorizeTest
 * @author  Michal Kuriata <michal.kuriata@smtsoftware.com>
 */
class UserFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UserFactory
     */
    protected $factory;

	/**
	 * setUp method
	 */
	public function setUp()
    {
        $this->factory = new UserFactory();
    }

	/**
	 * Testing:
	 * createService()
	 * UserHelper::setMvcKeeper()
	 * UserHelper::setControllerManager()
	 * UserHelper::setRouteStack()
	 *
	 * Should return configured helper
	 */
	public function testCreateService()
	{
		$sm     = Bootstrap::getServiceManager();
		$helper = $this->factory->createService($sm->get('ControllerManager'));

		$this->assertInstanceOf('Authorize\View\Helper\User', $helper);
		$this->assertEquals($helper->getMvcKeeper(), $sm->get('Authorize\MvcKeeper'));
		$this->assertEquals($helper->getControllerManager(), $sm->get('ControllerManager'));
		$this->assertEquals($helper->getRouteStack(), $sm->get('Router'));
	}
} 