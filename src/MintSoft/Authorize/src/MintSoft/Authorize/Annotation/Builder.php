<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 19.02.15
 * Time: 19:26
 */

namespace MintSoft\Authorize\Annotation;

class Builder
{
    /**
     * @var array Default annotations to register
     */
    protected $defaultAnnotations = [
        'MintSoft\Authorize\Annotation\Role',
        'MintSoft\Authorize\Annotation\Lockable',
        'MintSoft\Authorize\Annotation\Authenticated',
    ];

    /**
     * @var AnnotationReadable
     */
    protected $annotationReader;

    /**
     * @var array
     */
    protected $classes = [];

    public function __construct(AnnotationReadable $reader)
    {
        $this->annotationReader = $reader;
    }

    /**
     * @param $class
     *
     * @return $this
     */
    public function addClass($class)
    {
        if (!is_object($class) && !class_exists($class)) {
            throw new \InvalidArgumentException('Can not get annotations from class which not exists');
        }

        $this->classes[] = is_object($class) ? get_class($class) : $class;

        return $this;
    }

    protected function getAuthorizeAnnotations(AnnotationSet $set, $annotationCollection)
    {
        $out = false;
        foreach ($annotationCollection as $annotation) {
            if ($annotation instanceof Role) {
                $out = true;
                $set->addRole($annotation);
            } elseif ($annotation instanceof Lockable) {
//                $annotations['lock'] = $annotation;
            } elseif ($annotation instanceof Authenticated) {
                $out = true;
                $set->addAuthorize($annotation);
            }
        }

        return $out;
    }

    /**
     * @return AnnotationSet[]
     */
    public function build()
    {
        $out = [];
        foreach ($this->classes as $className) {
            $classSet        = new AnnotationSet($className);
            $classReflection = new \ReflectionClass($className);
            $this->getAuthorizeAnnotations($classSet, $this->annotationReader->getClassAnnotations($classReflection));
            foreach ($classReflection->getMethods() as $methodReflection) {
                $methodSet = new AnnotationSet($methodReflection->getName());
                if ($this->getAuthorizeAnnotations($methodSet, $this->annotationReader->getMethodAnnotations($methodReflection))) {
                    $classSet->addSet($methodSet);
                }
                $this->annotationReader->getMethodAnnotations($methodReflection);
            }

            $out[$className] = $classSet;
        }

        return $out;
    }
}
