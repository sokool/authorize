<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 03/06/14
 * Time: 14:25
 */

namespace AuthorizeTest;

use Authorize\Factory\RbacFactory;
use FloTest\Bootstrap;

class RbacFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RbacFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = new RbacFactory();
    }

    public function testInstanceReturned()
    {
        $this->assertInstanceOf(
            'Authorize\Service\RbacService',
            $this->factory->createService(Bootstrap::getServiceManager())
        );
    }
}
