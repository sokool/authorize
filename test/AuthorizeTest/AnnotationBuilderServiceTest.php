<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 03/06/14
 * Time: 15:45
 */

namespace AuthorizeTest;

use AuthorizeTest\Asset\Controller\TestBController;
use AuthorizeTest\Asset\Controller\TestController;
use MintSoft\Authorize\Annotation\AuthorizeBuilder;
use MintSoft\Authorize\ClassGuard;
use MintSoft\Authorize\Rbac\ZendRbac;
use MintSoft\Authorize\RoleProvider;
use Zend\Cache\Storage\Adapter\Memory;
use Zend\Code\Annotation\AnnotationCollection;
use Zend\Code\Annotation\AnnotationManager;
use Zend\Mvc\Controller\ControllerManager;

class AnnotationBuilderServiceTest extends \PHPUnit_Framework_TestCase
{

    const ANNOTATION_A_CLASS = 'AuthorizeTest\Asset\Controller\TestController';

    public function testAsd()
    {

        $classA        = get_class(new TestController());
        $classAMethod1 = 'someSpecificCustomAction';
        //$classAMethod1 = 'saveAction';

        $classB = get_class(new TestBController());

        $annotations = (new AuthorizeBuilder)
            ->addClass($classA)
            ->addClass($classB)
            ->buildAnnotations();

        $roles = $annotations[$classA]->getRoles($classAMethod1);


        $classGuard = new ClassGuard(new AuthorizeBuilder, new RoleProvider);
        $this->assertTrue($classGuard->isAllowed($classA, $classAMethod1, 'tomek'));
    }

    /**
     * @return AnnotationBuilder
     */
    public function createBuilder()
    {
        $controllerManager = (new ControllerManager())
            ->setInvokableClass('AuthorizeTest\Asset\Controller\Test', self::ANNOTATION_A_CLASS)
            ->setInvokableClass('AuthorizeTest\Asset\Controller\TestB', '\AuthorizeTest\Asset\Controller\TestBController')
            ->setServiceLocator(\Bootstrap::getServiceManager());

        return (new AnnotationBuilder($controllerManager))
            ->setCacheAdapter(new Memory());
    }

    public function testDefaultInstances()
    {
        $service = $this->createBuilder();
        // Check default agregated objects
        $this->assertInstanceOf('Zend\Code\Annotation\AnnotationManager', $service->getAnnotationManager());
        $this->assertInstanceOf('Zend\Cache\Storage\Adapter\Memory', $service->getCacheAdapter());
    }

    public function testSetUpInstances()
    {
        $service = $this->createBuilder();

        $service->setCacheAdapter($memoryCache = new Memory());
        $service->setAnnotationManager($annotationManager = new AnnotationManager());

        $this->assertSame($annotationManager, $service->getAnnotationManager());
        $this->assertSame($memoryCache, $service->getCacheAdapter());
    }

    /**
     * Testing:
     * getAuthorizeConfig()
     * getCacheAdapter()
     * MemoryCache::getItem()
     *
     * Setting some testing array to cache and check if method properly load same array from cache
     */
    public function testAuthorizeConfigCacheEmpty()
    {
        $service      = $this->createBuilder();
        $builderCache = $service->getCacheAdapter();
        $testingConf  = [
            'someConf' => true
        ];

        $builderCache->setItem(AnnotationBuilder::CACHE, serialize($testingConf));

        $service->getAuthorizeConfig();

        $this->assertEquals($testingConf, unserialize($builderCache->getItem(AnnotationBuilder::CACHE)));

        // clear settings
        $builderCache->setItem(AnnotationBuilder::CACHE, null);
    }

    public function testAuthorizeConfigCache()
    {
        $service      = $this->createBuilder();
        $builderCache = $service->getCacheAdapter();

        $this->assertNull($builderCache->getItem(AnnotationBuilder::CACHE));

        $service->getAuthorizeConfig();

        $this->assertTrue(is_string($builderCache->getItem(AnnotationBuilder::CACHE)));
    }

    public function testAuthorizeConfigStructure()
    {
        $authorizeConfig = $this->createBuilder()->getAuthorizeConfig();

        //Class name (FQDN) is key name of authorize config.
        $this->assertTrue(array_key_exists(self::ANNOTATION_A_CLASS, $authorizeConfig));
        //has methods array key
        $this->assertTrue(array_key_exists('methods', $authorizeConfig[self::ANNOTATION_A_CLASS]));
        //methods key is array
        $this->assertTrue(is_array($authorizeConfig[self::ANNOTATION_A_CLASS]['methods']));
        //has class array key
        $this->assertTrue(array_key_exists('class', $authorizeConfig[self::ANNOTATION_A_CLASS]));
        //class key has Authorize annotation
        $this->assertInstanceOf('MintSoft\Authorize\Annotation\Authorize', $authorizeConfig[self::ANNOTATION_A_CLASS]['class']);
        //has asset method array key
        $this->assertTrue(array_key_exists('someSpecificCustomAction', $authorizeConfig[self::ANNOTATION_A_CLASS]['methods']));
        //method has Authorize annotation
        $this->assertInstanceOf('MintSoft\Authorize\Annotation\Authorize', $authorizeConfig[self::ANNOTATION_A_CLASS]['methods']['someSpecificCustomAction']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBuildBasedOnStringException()
    {
        $this->createBuilder()->buildAnnotations('SomeClassFQDN');
    }

    /**
     * Testing:
     * testGetAuthorize()
     *
     * Main method's logic should found that wrong param was given and return null
     */
    public function testGetAuthorizeInvalidParam()
    {
        $service = $this->createBuilder();

        $annotationCollection = new AnnotationCollection;
        $annotationCollection->append('nonAuthorize');

        $class  = new \ReflectionClass('MintSoft\\Authorize\\Annotation\\AnnotationBuilder');
        $method = $class->getMethod('getAuthorize');
        $method->setAccessible(true);

        $result = $method->invoke($service, $annotationCollection);

        $this->assertNull($result);
    }
}