<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 03/06/14
 * Time: 14:04
 */

namespace AuthorizeTest;

use Authorize\Factory\CacheFactory;
use FloTest\Bootstrap;

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

    public function testInstanceReturned()
    {
        $this->assertInstanceOf(
            'Zend\Cache\Storage\Adapter\Memory',
            $this->factory->createService(Bootstrap::getServiceManager())
        );
    }

    public function testClearCacheAdapter()
    {
        $_GET['clearCache'] = true;
        $this->factory->createService(Bootstrap::getServiceManager());
    }
} 