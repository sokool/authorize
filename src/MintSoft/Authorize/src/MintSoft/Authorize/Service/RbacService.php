<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 14/05/14
 * Time: 14:16
 */

namespace MintSoft\Authorize\Service;

use MintSoft\Authorize\Provider\Permission\PermissionProviderInterface;
use MintSoft\Authorize\Provider\Role\RoleProviderInterface;
use Zend\Cache\Storage\Adapter\AbstractAdapter as CacheAdapter;
use Zend\Cache\Storage\Adapter\Memory as MemoryCache;
use Zend\Permissions\Rbac\Rbac as ZendRbac;
use Zend\Permissions\Rbac\Role;

class RbacService
{

    const CACHE_KEY = 'rbac-container';
    /**
     * @var PermissionProviderInterface
     */
    protected $permissionProvider;

    /**
     * @var RoleProviderInterface
     */
    protected $roleProvider;

    /**
     * @var \Zend\Permissions\Rbac
     */
    protected $container;

    /**
     * @var CacheAdapter|null
     */
    protected $cacheAdapter;

    public function __construct(RoleProviderInterface $roleProvider, PermissionProviderInterface $permissionProvider)
    {
        $this->roleProvider       = $roleProvider;
        $this->permissionProvider = $permissionProvider;
    }

    /**
     * @return PermissionProviderInterface
     */
    public function getPermissionProvider()
    {
        return $this->permissionProvider;
    }

    /**
     * @return RoleProviderInterface
     */
    public function getRoleProvider()
    {
        return $this->roleProvider;
    }

    /**
     * @param $user
     * @param $role
     *
     * @return bool
     */
    public function hasRole($user, $role)
    {
        if (!$this->getContainer()->hasRole($role)) {
            return false;
        }

        // @todo Cache it!
        $identityRoles = $this->getRoleProvider()->getByUser($user);
        foreach ($identityRoles as $identityRole) {
            if ($identityRole == $role) {
                return true;
            }
        }

        return false;
    }

    public function hasPermission($user, $permission)
    {
        // @todo Cache it?
        $identityRoles = $this->getRoleProvider()->getByUser($user);
        $rbacContainer = $this->getContainer();
        foreach ($identityRoles as $identityRole) {
            if (!$rbacContainer->hasRole($identityRole)) {
                return false;
            }

            if ($rbacContainer->getRole($identityRole)->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return \Zend\Permissions\Rbac\Rbac
     */
    private function getContainer()
    {
        if (!$this->container) {
            $cacheAdapter = $this->getCacheAdapter();
            if ($cacheAdapter->hasItem(self::CACHE_KEY)) {
                $this->container = unserialize($cacheAdapter->getItem(self::CACHE_KEY));
            } else {
                $this->container = new ZendRbac();
                foreach ($this->getPermissionProvider()->getAll() as $permissionName => $roles) {
                    foreach ($roles as $roleName => $role) {
                        if ($this->container->hasRole($roleName)) {
                            $role = $this->container->getRole($roleName);
                        } else {
                            $role = new Role($roleName);
                            $this->container->addRole($role);
                        }
                        $role->addPermission($permissionName);
                    }
                }
                $cacheAdapter->setItem(self::CACHE_KEY, serialize($this->container));
            }
        }

        return $this->container;
    }

    /**
     * @param CacheAdapter $cache
     *
     * @return $this
     */
    public function setCacheAdapter(CacheAdapter $cache)
    {
        $this->cacheAdapter = $cache;

        return $this;
    }

    /**
     * @return null|CacheAdapter
     */
    public function getCacheAdapter()
    {
        if (!$this->cacheAdapter) {
            return $this->cacheAdapter = new MemoryCache();
        }

        return $this->cacheAdapter;
    }
}