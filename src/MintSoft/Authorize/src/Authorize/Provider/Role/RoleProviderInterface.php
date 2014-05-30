<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 14/05/14
 * Time: 13:56
 */
namespace Authorize\Provider\Role;

interface RoleProviderInterface
{
    public function getAll();

    public function getByUser($user);
}