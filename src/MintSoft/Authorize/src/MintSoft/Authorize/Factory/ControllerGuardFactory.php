<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 29.11.14
 * Time: 14:26
 */

namespace MintSoft\Authorize\Factory;

use MintSoft\Authorize\Annotation\AnnotationBuilder;
use MintSoft\Authorize\Authorize;
use MintSoft\Authorize\Mvc\ControllerGuard;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ControllerGuardFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $authorizeBuilder AnnotationBuilder */
        /** @var $controllerManager ControllerManager */
        /** @var Authorize $authorize */
        $controllerManager = $serviceLocator->get('ControllerManager');
        $authorize         = $serviceLocator->get('MintSoft\Authorize');
        $annotationBuilder = new AnnotationBuilder();
        $cacheAdapter      = $serviceLocator->get('MintSoft\Authorize\Cache');

        return (new ControllerGuard($authorize, $controllerManager))
            ->setAuthorizeBuilder($annotationBuilder)
            ->setCacheAdapter($cacheAdapter);
    }
}