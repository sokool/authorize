<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 03/06/14
 * Time: 14:28
 */

namespace AuthorizeTest;

use Authorize\Factory\AccessForbiddenFactory;
use FloTest\Bootstrap;

class AccessForbiddenFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AccessForbiddenFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = new AccessForbiddenFactory();
    }

    public function testInstanceReturned()
    {
        $this->assertInstanceOf(
            'Authorize\View\AccessForbiddenStrategy',
            $this->factory->createService(Bootstrap::getServiceManager())
        );
    }
} 