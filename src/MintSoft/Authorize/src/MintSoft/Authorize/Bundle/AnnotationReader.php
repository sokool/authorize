<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 19.02.15
 * Time: 20:34
 */

namespace MintSoft\Authorize\Bundle;

use Doctrine\Common\Annotations\Reader;
use MintSoft\Authorize\Annotation\AnnotationReadable;

class AnnotationReader implements AnnotationReadable
{
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function getClassAnnotations(\ReflectionClass $class)
    {
        return $this->reader->getClassAnnotations($class);
    }

    public function getMethodAnnotations(\ReflectionMethod $method)
    {
        return $this->reader->getMethodAnnotations($method);
    }
}