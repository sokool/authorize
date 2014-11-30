<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 30.11.14
 * Time: 10:21
 */

namespace MintSoft\Authorize\Mvc\Controller\Plugin\Service;

use MintSoft\Authorize\Mvc\Controller\Plugin\Identity;
use Nette\Diagnostics\Debugger;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IdentityFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $services = $serviceLocator->getServiceLocator();
        $helper   = new Identity($services->get('MintSoft\Authorize\ControllerGuard'));
        $helper->setRouter($services->get('Router'));
        if ($services->has('Zend\Authentication\AuthenticationService')) {
            $helper->setAuthenticationService($services->get('Zend\Authentication\AuthenticationService'));
        }

        return $helper;
    }
}