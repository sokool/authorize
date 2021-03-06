<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 03/06/14
 * Time: 14:28
 */

namespace AuthorizeTest;

use MintSoft\Authorize\Factory\AccessForbiddenFactory;

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
            'MintSoft\Authorize\View\AccessForbiddenStrategy',
            $this->factory->createService(\Bootstrap::getServiceManager())
        );
    }
} 