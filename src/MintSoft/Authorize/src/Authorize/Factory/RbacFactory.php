<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 14/05/14
 * Time: 14:20
 */

namespace Authorize\Factory;

use Authorize\Service\RbacService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RbacFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $permissionProvider = $serviceLocator->get('Authorize\Provider\Permission');
        $roleProvider       = $serviceLocator->get('Authorize\Provider\Role');
        $cacheService       = $serviceLocator->get('Authorize\Cache');

        $rbacService = new RbacService($roleProvider, $permissionProvider);
        $rbacService->setCacheAdapter($cacheService);

        return $rbacService;
    }
}