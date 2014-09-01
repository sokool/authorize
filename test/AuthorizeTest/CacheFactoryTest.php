<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 03/06/14
 * Time: 14:04
 */

namespace AuthorizeTest;

use FloTest\Bootstrap;
use MintSoft\Authorize\Factory\CacheFactory;

class CacheFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CacheFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = new CacheFactory();
    }

	/**
	 * Testing:
	 * createService()
	 *
	 * Should return new instance of MemoryCache when no cache in configuration
	 */
	public function testCreateService()
	{
		$configuration = [
			'authorize' => []
		];

		$mockedSM = $this->getMock(get_class(\Bootstrap::getServiceManager()), ['get']);
		$mockedSM->expects($this->any())
			->method('get')
			->will($this->returnValue($configuration));

		$this->assertInstanceOf(
			'Zend\Cache\Storage\Adapter\Memory',
			$this->factory->createService($mockedSM)
		);
	}

	public function testInstanceReturned()
	{
		$this->assertInstanceOf(
			'Zend\Cache\Storage\Adapter\Memory',
			$this->factory->createService(\Bootstrap::getServiceManager())
		);
	}

    public function testClearCacheAdapter()
    {
        $_GET['clearCache'] = true;
        $this->factory->createService(\Bootstrap::getServiceManager());
    }
} 