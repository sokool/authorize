<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 29.11.14
 * Time: 18:35
 */

namespace MintSoft\Authorize\Annotation;

/**
 * @Annotation
 */
final class Authenticated
{
    private $name;

    function __construct(array $value)
    {
        $this->name = reset($value);
    }

    public function getName()
    {
        return $this->name;
    }
}