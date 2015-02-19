<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 19.02.15
 * Time: 19:18
 */

namespace MintSoft\Authorize\Annotation;

interface AnnotationReadable
{
    public function getClassAnnotations(\ReflectionClass $class);

    public function getMethodAnnotations(\ReflectionMethod $method);
}