<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 20/05/14
 * Time: 09:42
 */

namespace MintSoft\Authorize\Factory;

use MintSoft\Authorize\Annotation\AuthorizeBuilder;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthorizeBuilderFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new AuthorizeBuilder;
    }
}