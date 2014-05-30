<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 15/05/14
 * Time: 13:18
 */

namespace MintSoft\Authorize\Annotation;

use Zend\Form\Annotation\AbstractArrayOrStringAnnotation;

/**
 * @Annotation
 */
final class Authorize extends AbstractArrayOrStringAnnotation
{

    public function __construct(array $data)
    {
        if (empty($data)) {
            $data = ['value' => []];
        }

        parent::__construct($data);
    }

    public function isEmpty()
    {
        return empty($this->value);
    }

    public function hasRoles()
    {
        return !empty($this->value['roles']);
    }

    public function hasUsers()
    {
        return !empty($this->value['users']);
    }

    public function hasPermissions()
    {
        return !empty($this->value['permissions']);
    }

    public function getRoles()
    {
        if (!$this->hasRoles()) {
            return [];
        }

        if (is_string($this->value['roles'])) {
            return [$this->value['roles']];
        }

        return $this->value['roles'];
    }

    public function getUsers()
    {
        if (!$this->hasUsers()) {
            return [];
        }

        if (is_string($this->value['users'])) {
            return [$this->value['users']];
        }

        return $this->value['users'];
    }

    public function getPermissions()
    {
        if (!$this->hasPermissions()) {
            return [];
        }

        if (is_string($this->value['permissions'])) {
            return [$this->value['permissions']];
        }

        return $this->value['permissions'];
    }
} 