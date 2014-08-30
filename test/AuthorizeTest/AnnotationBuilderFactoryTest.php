<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 03/06/14
 * Time: 14:31
 */

namespace AuthorizeTest;

use FloTest\Bootstrap;
use MintSoft\Authorize\Factory\BuilderFactory;

class AnnotationBuilderFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BuilderFactory
     */
    protected $factory;

    public function setUp()
    {
        Bootstrap::getServiceManager()
            ->setAllowOverride(true)
            ->setFactory('Configuration', function () {
                return [
                    'authorize' => [
                        'cache' => [
                            'adapter' => [
                                'name' => 'memory',
                            ],
                        ],
                    ],
                ];
            });

        $this->factory = new BuilderFactory();
    }

    public function testInstanceReturned()
    {
        $this->assertInstanceOf(
            'Authorize\Annotation\AnnotationBuilder',
            $this->factory->createService(Bootstrap::getServiceManager())
        );
    }
} 