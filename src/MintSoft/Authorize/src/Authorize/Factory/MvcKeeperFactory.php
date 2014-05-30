<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 15/05/14
 * Time: 13:20
 */

namespace Authorize\Factory;

use Authorize\Service\MvcKeeper;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MvcKeeperFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $authenticationService \Zend\Authentication\AuthenticationService */
        $authenticationService = $serviceLocator->get('Zend\Authentication\AuthenticationService');
        $rbacService           = $serviceLocator->get('Authorize\Rbac');
        $annotationBuilder     = $serviceLocator->get('Authorize\Annotation\Builder');

        $mvcKeeper = new MvcKeeper($rbacService, $authenticationService, $annotationBuilder);

        return $mvcKeeper;
    }
}