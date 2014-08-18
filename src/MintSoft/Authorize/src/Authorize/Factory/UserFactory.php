<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 20/06/14
 * Time: 14:06
 */

namespace MintSoft\Authorize\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Authorize\View\Helper\User as UserHelper;

class UserFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
		$serviceManager = $serviceLocator->getServiceLocator();
        $helper         = new UserHelper();
        $helper->setMvcKeeper($serviceManager->get('Authorize\MvcKeeper'));
        $helper->setControllerManager($serviceManager->get('ControllerManager'));
        $helper->setRouteStack($serviceManager->get('Router'));

        return $helper;
    }
}