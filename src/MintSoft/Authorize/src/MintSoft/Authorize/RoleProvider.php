<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 29.11.14
 * Time: 17:05
 */

namespace MintSoft\Authorize;

use Nette\Diagnostics\Debugger;
use Zend\Debug\Debug;
use Zend\Permissions\Rbac\Role;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RoleProvider implements RoleProvidable, FactoryInterface
{
    protected $sm;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new self;
    }

    /**
     * @param $identity
     *
     * @return Role[]
     */
    public function allRoles($identity)
    {
        if (!$identity) {
            return [];
        }

        return [
            'Administrator' => ['edit', 'create', 'view'],
            'Editor'        => [],
            'Root'          => [],
        ];
    }
}