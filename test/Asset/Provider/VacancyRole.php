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

class VacancyRole extends Role
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this;
    }

    public function getAll()
    {
        return ['Vacancy Fill'];
    }

    public function getByUser($user)
    {
        $userRoles = [
            'some-user@some-domain.here' => ['Vacancy Fill'],
        ];

        return array_key_exists($user, $userRoles) ? $userRoles[$user] : [];
    }
}