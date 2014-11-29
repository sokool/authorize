<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 29.11.14
 * Time: 16:52
 */

namespace MintSoft\Authorize;

use Zend\Permissions\Rbac\Role;

interface RoleProvidable
{
    /**
     * @param $identity
     *
     * @return Role[]
     */
    public function allRoles($identity);
} 