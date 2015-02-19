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
use Zend\Permissions\Rbac\Rbac;
use Zend\Permissions\Rbac\Role;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RoleProvider implements RoleProvidable
{
    /**
     * Determine
     *
     * @return boolean
     */
    public function refresh()
    {
        echo 'refresh';

        return true;
    }

    /**
     * @param $identity
     *
     * @return Role[]
     */
    public function allRoles($identity)
    {
        $container = new Rbac;
        if (!$identity) {
            return $container;
        }

        $container->addRole(
            (new Role('Manager'))
                ->addPermission('ManageWorkStation')
        );

        $dispatcher = (new Role('Dispatcher'))
            ->addPermission('ManageTransport');

        return $container
            ->addRole($dispatcher);
    }
}