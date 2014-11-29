<?php
namespace MintSoft\Authorize;

use Zend\EventManager\EventInterface;
use Zend\Http\Response as HttpResponse;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;

class Module implements
    ServiceProviderInterface,
    ConfigProviderInterface,
    ViewHelperProviderInterface
{

    public function getConfig()
    {
        return include __DIR__ . '/../../../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return include __DIR__ . '/../../../config/service.config.php';
    }

    public function getViewHelperConfig()
    {
        return include __DIR__ . '/../../../config/view_helper.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', DIRECTORY_SEPARATOR, __NAMESPACE__),
                ],
            ],
        ];
    }
}
