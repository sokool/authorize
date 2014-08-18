<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 03/06/14
 * Time: 12:47
 */

namespace AuthorizeTest\Asset\Provider;

use Zend\ServiceManager\ServiceLocatorInterface;

class VacancyPermission extends Permission
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
            'render-form' => [
                'Vacancy Fill' => true,
            ],
        ];
    }
}