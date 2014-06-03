<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 03/06/14
 * Time: 15:45
 */

namespace AuthorizeTest;

use Authorize\Annotation\AnnotationBuilder;
use Authorize\Factory\BuilderFactory;
use FloTest\Bootstrap;
use Zend\Cache\Storage\Adapter\Memory;
use Zend\Code\Annotation\AnnotationManager;

class AnnotationBuilderServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return AnnotationBuilder
     */
    public function createBuilder()
    {
        Bootstrap::getServiceManager()
            ->get('ControllerManager')
            ->setAllowOverride(true)
            ->setInvokableClass('AuthorizeTest\Controller\Test', 'AuthorizeTest\Controller\TestController');

        return (new BuilderFactory())->createService(Bootstrap::getServiceManager());
    }

    public function testDefaultInstances()
    {
        $service = new AnnotationBuilder(Bootstrap::getServiceManager()->get('ControllerManager'));

        // Check default agregated objects
        $this->assertInstanceOf('Zend\Code\Annotation\AnnotationManager', $service->getAnnotationManager());
        $this->assertInstanceOf('Zend\Cache\Storage\Adapter\Memory', $service->getCacheAdapter());
    }

    public function testSetUpInstances()
    {
        $service = new AnnotationBuilder(Bootstrap::getServiceManager()->get('ControllerManager'));

        $service->setCacheAdapter($memoryCache = new Memory());
        $service->setAnnotationManager($annotationManager = new AnnotationManager());

        $this->assertSame($annotationManager, $service->getAnnotationManager());
        $this->assertSame($memoryCache, $service->getCacheAdapter());
    }

    public function testAuthorizeConfigCache()
    {
        $service      = $this->createBuilder();
        $builderCache = $service->getCacheAdapter();

        $this->assertNull($builderCache->getItem(AnnotationBuilder::CACHE));

        $service->getAuthorizeConfig();

        $this->assertTrue(is_string($builderCache->getItem(AnnotationBuilder::CACHE)));
    }
} 