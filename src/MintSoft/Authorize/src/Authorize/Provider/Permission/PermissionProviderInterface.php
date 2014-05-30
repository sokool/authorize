<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 14/05/14
 * Time: 13:59
 */

namespace Authorize\Provider\Permission;

interface PermissionProviderInterface
{
    /**
     * Return array structure $array[permission-name][role-name];
     *
     * @return array
     */
    public function getAll();
} 