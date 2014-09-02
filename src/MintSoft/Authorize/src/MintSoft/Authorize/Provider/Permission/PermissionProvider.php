<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 02/09/14
 * Time: 23:24
 */

namespace MintSoft\Authorize\Provider\Permission;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PermissionProvider implements PermissionProviderInterface, FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this;
    }


    /**
     * Return array structure $array[permission-name][role-name];
     *
     * @return array
     */
    public function getAll()
    {
        return [
            'all' => [
                'SuperUser' => true,
            ],
        ];
    }

} 