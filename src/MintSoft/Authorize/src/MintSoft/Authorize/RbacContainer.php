<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 19.02.15
 * Time: 10:21
 */

namespace MintSoft\Authorize;

interface RbacContainer
{
    public function hasRole($objectOrName);

    public function isGranted($role, $permission, $assert = null);
}