<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 20/05/14
 * Time: 09:42
 */

namespace Authorize\Factory;

use Authorize\Annotation\AnnotationBuilder;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BuilderFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $cache             = $serviceLocator->get('Authorize\Cache');
        $controllerManager = $serviceLocator->get('ControllerManager');

        $annotationBuilder = new AnnotationBuilder($controllerManager);
        $annotationBuilder->setCacheAdapter($cache);

        return $annotationBuilder;
    }
}