<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 19/05/14
 * Time: 10:38
 */

namespace MintSoft\Authorize\Factory;

use MintSoft\Authorize\View\AccessForbiddenStrategy;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AccessForbiddenFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new AccessForbiddenStrategy();
    }
}