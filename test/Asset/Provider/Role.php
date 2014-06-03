<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 03/06/14
 * Time: 12:48
 */

namespace AuthorizeTest\Asset\Provider;

use Authorize\Provider\Role\RoleProviderInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Role implements RoleProviderInterface, FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this;
    }

    public function getAll()
    {
        return ['Upload', 'Download', 'Grant'];
    }

    public function getByUser($user)
    {
        $userRoles = [
            'some-user@some-domain.here' => ['Upload', 'Download', 'Some amazing role']
        ];

        return array_key_exists($user, $userRoles) ? $userRoles[$user] : [];
    }
}