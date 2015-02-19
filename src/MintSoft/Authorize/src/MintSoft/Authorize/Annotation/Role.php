<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 29.11.14
 * Time: 18:27
 */

namespace MintSoft\Authorize\Annotation;

/**
 * @Annotation
 */
final class Role
{
    private $value = [];

    public function __construct($value)
    {
        $this->value = $value['value'];
    }

    public function getName()
    {
        return is_array($this->value) ? $this->value[0] : $this->value;
    }

    public function hasPermissions()
    {
        return array_key_exists(1, (array) $this->value);
    }

    public function getPermissions()
    {
        if (!$this->hasPermissions()) {
            return [];
        }

        return $this->value[1];
    }
}