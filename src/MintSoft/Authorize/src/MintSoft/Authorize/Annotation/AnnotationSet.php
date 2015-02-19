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
    protected $name;

    /**
     * @var Role[]
     */
    protected $roles = [];

    /**
     * @var Authenticated[]
     */
    protected $authenticated;

    /**
     * @var AnnotationSet[]
     */
    protected $sets = [];

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function addSet(AnnotationSet $set)
    {
        $this->sets[$set->getName()] = $set;
    }

    public function getSets()
    {
        return $this->sets;
    }

    public function getSet($name)
    {
        if (!isset($this->sets[$name])) {
            return null;
        }

        return $this->sets[$name];
    }

    public function addRole(Role $role)
    {
        $this->roles[$role->getName()] = $role;

        return $this;
    }

    public function addAuthorize(Authenticated $authenticated)
    {
        $this->authenticated = $authenticated;

        return $this;
    }

    /**
     * @return Role[]
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function getRole($name)
    {
        if (empty($this->roles[$name])) {
            return null;
        }

        return $this->roles[$name];
    }

    /**
     * @param $namespace
     *
     * @return Authenticated|null
     */
    public function getAuthenticated()
    {
        if (!isset($this->authenticated)) {
            return null;
        }

        return $this->authenticated;
    }
}