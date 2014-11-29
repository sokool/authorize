<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 29.11.14
 * Time: 11:57
 */

namespace MintSoft\Authorize\Factory;

use MintSoft\Authorize\Mvc\AuthorizeListener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthorizeListenerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new AuthorizeListener();
    }
}