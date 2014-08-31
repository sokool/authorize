<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 15/05/14
 * Time: 13:20
 */

namespace MintSoft\Authorize\Factory;

use MintSoft\Authorize\Service\MvcKeeper;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MvcKeeperFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $authenticationService \Zend\Authentication\AuthenticationService */
        $authenticationService = $serviceLocator->get('Zend\Authentication\AuthenticationService');
        $rbacService           = $serviceLocator->get('MintSoft\Authorize\Rbac');
        $annotationBuilder     = $serviceLocator->get('MintSoft\Authorize\Annotation\Builder');

        $mvcKeeper = new MvcKeeper($rbacService, $authenticationService, $annotationBuilder);

        return $mvcKeeper;
    }
}