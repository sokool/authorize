<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 03/06/14
 * Time: 12:47
 */

namespace AuthorizeTest\Asset\Provider;

use Authorize\Provider\Permission\PermissionProviderInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Permission implements PermissionProviderInterface, FactoryInterface
{
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
            'render-form'         => [
                'Upload'   => true,
                'Download' => true,
            ],
            'render-default-page' => [
                'Upload'   => true,
                'Download' => true,
                'Grant'    => true,
            ],
            'grant-for-all-users' => [
                'Grant' => true,
            ]
        ];
    }
}