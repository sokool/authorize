<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 29.11.14
 * Time: 15:32
 */

namespace MintSoft\Authorize;

use Nette\Diagnostics\Debugger;
use Zend\Cache\Storage\Adapter\AbstractAdapter as CacheAdapter;
use Zend\Cache\Storage\Adapter\Memory as MemoryCache;
use Zend\Permissions\Rbac\Rbac;
use Zend\Permissions\Rbac\Role;

class Authorize
{
    /**
     * @var CacheAdapter
     */
    protected $cacheAdapter;

    /**
     * @var RoleProvidable
     */
    protected $roleProvider;

    public function __construct(RoleProvidable $roleProvider)
    {
        $this->roleProvider = $roleProvider;
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
            $this->setCacheAdapter(new MemoryCache);
        }

        return $this->cacheAdapter;
    }

    /**
     * @return RoleProvidable
     */
    public function getRoleProvider()
    {
        return $this->roleProvider;
    }

    /**
     * @param RoleProvidable $roleProvider
     */
    public function setRoleProvider(RoleProvidable $roleProvider)
    {
        $this->roleProvider = $roleProvider;
    }

    /**
     * @param array $roles
     *
     * @return Rbac
     */
    private function buildRbac(array $roles)
    {
        $container = new Rbac();
        foreach ($roles as $roleName => $role) {
            if (is_array($role)) {
                $permissions = $role;
                $role        = new Role($roleName);
                foreach ($permissions as $permission) {
                    $role->addPermission($permission);
                }
            }
            $container->addRole($role);
        }

        return $container;
    }

    /**
     * @param $identity
     *
     * @return Rbac
     */
    protected function getRbac($identity)
    {
        $cacheKey = is_string($identity) ? $identity : null;
        $cacheKey = is_object($identity) ? spl_object_hash($identity) : $cacheKey;

        $cacheAdapter = $this->getCacheAdapter();
        if ($cacheKey) {
            $container = $cacheAdapter->getItem($cacheKey);
            if (!empty($container)) {
                return unserialize($container);
            }
        }

        $container = $this->buildRbac($this->getRoleProvider()->allRoles($identity));
        $cacheAdapter->setItem($cacheKey, serialize($container));

        return $container;
    }

    /**
     * @param $identity
     * @param $role
     *
     * @return bool
     */
    public function hasRole($identity, $role)
    {
        if ($identity == null) {
            return false;
        }

        return $this
            ->getRbac($identity)
            ->hasRole($role);
    }

    /**
     * @param $identity
     * @param $role
     * @param $permission
     *
     * @return bool
     */
    public function isGranted($identity, $role, $permission)
    {
        if ($identity == null) {
            return false;
        }

        return $this
            ->getRbac($identity)
            ->isGranted($role, $permission);
    }
}