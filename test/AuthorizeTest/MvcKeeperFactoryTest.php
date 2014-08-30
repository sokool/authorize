<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 03/06/14
 * Time: 14:36
 */

namespace AuthorizeTest;

use FloTest\Bootstrap;
use MintSoft\Authorize\Factory\MvcKeeperFactory;

class MvcKeeperFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MvcKeeperFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = new MvcKeeperFactory();
    }

    public function testInstanceReturned()
    {
        $this->assertInstanceOf(
            'Authorize\Service\MvcKeeper',
            $this->factory->createService(Bootstrap::getServiceManager())
        );
    }
} 