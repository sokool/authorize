<?php

include __DIR__ . '/../vendor/autoload.php';

use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;

final class Bootstrap
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    public static $serviceManager;

    public static function autoloader()
    {
        $config = [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    'AuthorizeTest' => __DIR__ . '/AuthorizeTest',
                ],
            ]
        ];

        AutoloaderFactory::factory($config);
    }

    public static function init()
    {
        self::autoloader();

        $serviceManager = new ServiceManager(new ServiceManagerConfig());
        $serviceManager->setService('ApplicationConfig', include __DIR__ . '/application.config.php');
        $serviceManager->get('ModuleManager')->loadModules();

        self::$serviceManager = $serviceManager;
    }

    /**
     * @return ZendServiceManager
     */
    public static function getServiceManager()
    {
        return self::$serviceManager;
    }
}

Bootstrap::init();


