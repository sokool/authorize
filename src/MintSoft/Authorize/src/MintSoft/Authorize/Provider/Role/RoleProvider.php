<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 02/09/14
 * Time: 23:25
 */

namespace MintSoft\Authorize\Provider\Role;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RoleProvider implements RoleProviderInterface, FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this;
    }

    public function getAll()
    {
        return ['SuperUser'];
    }

    public function getByUser($user)
    {
        return ['SuperUser'];
    }
} 