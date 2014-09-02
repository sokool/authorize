<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 14/05/14
 * Time: 14:20
 */

namespace MintSoft\Authorize\Factory;

use MintSoft\Authorize\Service\RbacService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RbacFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $permissionProvider = $serviceLocator->get('MintSoft\Authorize\Provider\Permission');
        $roleProvider       = $serviceLocator->get('MintSoft\Authorize\Provider\Role');
        $cacheService       = $serviceLocator->get('MintSoft\Authorize\Cache');

        $rbacService = new RbacService($roleProvider, $permissionProvider);
        $rbacService->setCacheAdapter($cacheService);

        return $rbacService;
    }
}