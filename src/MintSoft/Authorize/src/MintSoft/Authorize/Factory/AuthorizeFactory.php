<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 29.11.14
 * Time: 19:53
 */

namespace MintSoft\Authorize\Factory;

use MintSoft\Authorize\Authorize;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthorizeFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $roleProvider = $serviceLocator->get('MintSoft\Authorize\RoleProvider');
        $cache        = $serviceLocator->get('MintSoft\Authorize\Cache');

        return (new Authorize($roleProvider))
            ->setCacheAdapter($cache);
    }
}