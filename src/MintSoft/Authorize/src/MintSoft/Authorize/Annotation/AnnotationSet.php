<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 19.02.15
 * Time: 10:24
 */

namespace MintSoft\Authorize\Annotation;

class AnnotationSet
{
    /**
     * @var Role[]
     */
    protected $roles;

    /**
     * @var Authenticated[]
     */
    protected $authenticated;

    public function addRole(Role $role, $namespace)
    {
        $this->roles[$namespace][$role->getName()] = $role;

        return $this;
    }

    public function addAuthorize(Authenticated $authenticated, $namespace)
    {
        $this->authenticated[$namespace] = $authenticated;

        return $this;
    }

    /**
     * @param $namespace
     *
     * @return Role[]
     */
    public function getRoles($namespace)
    {
        if (!isset($this->roles[$namespace])) {
            return [];
        }

        return $this->roles[$namespace];
    }

    /**
     * @param $namespace
     *
     * @return Authenticated|null
     */
    public function getAuthenticated($namespace)
    {
        if (!isset($this->authenticated[$namespace])) {
            return null;
        }

        return $this->authenticated[$namespace];
    }
}