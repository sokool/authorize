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
    BootstrapListenerInterface,
    ConfigProviderInterface,
    ViewHelperProviderInterface
{

    public function onBootstrap(EventInterface $e)
    {
        //Authorize module works only with Http Response.
        if (!$e->getResponse() instanceof HttpResponse) {
            return;
        }

        $eventManager   = $e->getTarget()->getEventManager();
        $serviceManager = $e->getTarget()->getServiceManager();

        $eventManager->attach($serviceManager->get('MintSoft\Authorize\MvcKeeper'));
        $eventManager->attach($serviceManager->get('MintSoft\Authorize\AccessForbiddenStrategy'));
    }

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
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }
}
