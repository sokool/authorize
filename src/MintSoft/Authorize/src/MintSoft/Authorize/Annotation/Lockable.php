<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 29.11.14
 * Time: 18:48
 */

namespace MintSoft\Authorize\Annotation;

use Nette\Diagnostics\Debugger;

/**
 * @Annotation
 */
final class Lockable
{
    private $locked;

    function __construct(array $value)
    {
        $this->locked = empty($value) ? true : false;
    }

    public function isLocked()
    {
        return $this->locked;
    }
} 