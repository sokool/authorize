<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 29.11.14
 * Time: 16:52
 */

namespace MintSoft\Authorize;

use Zend\Permissions\Rbac\Rbac;

interface RoleProvidable
{
    /**
     * Determine
     *
     * @return boolean
     */
    public function refresh();

    /**
     * @param $identity
     *
     * @return Rbac
     */
    public function allRoles($identity);
} 