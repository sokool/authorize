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
use MintSoft\Authorize\Annotation\AuthorizeBuilder;
use MintSoft\Authorize\Annotation\Role;
use Zend\Permissions\Rbac\Rbac;

class ClassGuard
{
    protected $annotationBuilder;

    protected $roleProvider;

    /**
     * @param AuthorizeBuilder $builder
     * @param RoleProvidable   $roleProvider
     */
    public function __construct(AuthorizeBuilder $builder, RoleProvidable $roleProvider)
    {
        $this->annotationBuilder = $builder;
        $this->roleProvider      = $roleProvider;
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
     * @param string        $namespace
     * @param string        $identity
     *
     * @return bool
     */
    private function checkBlock(AnnotationSet $classAnnotations, $namespace, $identity)
    {
        $allowed         = true;
        $container       = $this->getContainer($identity);
        $authAnnotation  = $classAnnotations->getAuthenticated($namespace);
        $roleAnnotations = $classAnnotations->getRoles($namespace);

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
     * @return Rbac
     */
    private function getContainer($identity)
    {
        if ($this->roleProvider->refresh()) {
            return $this->roleProvider->allRoles($identity);
        }
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
        $classAnnotations = $this->annotationBuilder->addClass($className)->buildAnnotations()[$className];

        //Check on ClassName Level
        $allowed = $this->checkBlock($classAnnotations, $className, $identity);
        if ($allowed) {
            //Check on ClassMethodName Level
            if ($methodName) {
                return $this->checkBlock($classAnnotations, $methodName, $identity);
            }
        }

        return $allowed;
    }
}