<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 19.02.15
 * Time: 10:57
 */

namespace MintSoft\Authorize;

use MintSoft\Authorize\Annotation\AnnotationSet;
use MintSoft\Authorize\Annotation\Authenticated;
use MintSoft\Authorize\Annotation\Builder as AnnotationBuilder;
use MintSoft\Authorize\Annotation\Role;
use Zend\Permissions\Rbac\Rbac;

class ClassGuard
{
    protected $builder;

    protected $provider;

    protected $containers;

    /**
     * @param AnnotationBuilder $builder
     * @param RoleProvidable    $roleProvider
     */
    public function __construct(AnnotationBuilder $builder, RoleProvidable $roleProvider)
    {
        $this->builder  = $builder;
        $this->provider = $roleProvider;
    }

    /**
     * @param Authenticated $authenticated
     * @param string        $identity
     *
     * @return bool
     */
    private function checkAuthentication(Authenticated $authenticated = null, $identity = null)
    {
        if ($authenticated === null) {
            return true;
        }

        if ($identity === null) {
            return false;
        }

        $authenticatedName = $authenticated->getName();

        return empty($authenticatedName) ? true : $identity === $authenticatedName;
    }

    /**
     * @param Role $role
     * @param Rbac $container
     *
     * @return bool
     */
    private function checkRole(Role $role, Rbac $container)
    {
        $allowed  = true;
        $roleName = $role->getName();
        if (!$container->hasRole($roleName)) {
            return false;
        }

        if (($permissions = $role->getPermissions())) {
            $allowed = false;
            foreach ($permissions as $permission) {
                if ($container->isGranted($roleName, $permission)) {
                    return true;
                }
            }
        }

        return $allowed;
    }

    /**
     * @param AnnotationSet $classAnnotations
     * @param string        $identity
     *
     * @return bool
     */
    private function checkBlock(AnnotationSet $classAnnotations, $identity)
    {
        $allowed         = true;
        $container       = $this->getContainer($identity);
        $authAnnotation  = $classAnnotations->getAuthenticated();
        $roleAnnotations = $classAnnotations->getRoles();

        if (!$this->checkAuthentication($authAnnotation, $identity)) {
            return false;
        }

        foreach ($roleAnnotations as $role) {
            $allowed = false;
            if ($this->checkRole($role, $container)) {
                return true;
            }
        }

        return $allowed;
    }

    /**
     * @param $identity
     *
     * @return mixed
     * @throws \Exception
     */
    private function getContainer($identity)
    {
        $hash = null;
        if (is_string($identity)) {
            $hash = $identity;
        }

        if (is_object($identity)) {
            $hash = spl_object_hash($identity);
        }

        if (!empty($this->containers[$hash])) {
            return $this->containers[$hash];
        }

        if (is_null($hash)) {
            throw new \Exception('Type is not supported');
        }
        $this->containers[$hash] = $this->provider->allRoles($identity);

        return $this->containers[$hash];
    }

    /**
     * @param      $className
     * @param null $methodName
     * @param null $identity
     *
     * @return bool
     */
    public function isAllowed($className, $methodName = null, $identity = null)
    {
        $classAnnotations = $this->builder->addClass($className)->build()[$className];

        //Check on ClassName Level
        $allowed = $this->checkBlock($classAnnotations, $identity);
        if ($allowed) {
            //Check on ClassMethodName Level
            if ($methodName && ($methodAnnotationSet = $classAnnotations->getSet($methodName))) {
                return $this->checkBlock($classAnnotations->getSet($methodName), $identity);
            }
        }

        return $allowed;
    }
}