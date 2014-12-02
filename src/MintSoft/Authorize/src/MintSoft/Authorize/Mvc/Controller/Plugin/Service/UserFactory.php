<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 30.11.14
 * Time: 10:21
 */

namespace MintSoft\Authorize\Mvc\Controller\Plugin\Service;

use MintSoft\Authorize\Mvc\Controller\Plugin\User;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserFactory implements FactoryInterface
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
        $helper   = new User($services->get('MintSoft\Authorize\ControllerGuard'));
        $helper->setRouter($services->get('Router'));
        if ($services->has('Zend\Authentication\AuthenticationService')) {
            $helper->setIdentity($services->get('Zend\Authentication\AuthenticationService')->getIdentity());
        }

        return $helper;
    }
}