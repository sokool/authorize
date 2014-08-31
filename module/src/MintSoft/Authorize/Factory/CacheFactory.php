<?php
/**
 * Created by PhpStorm.
 * User: sokool
 * Date: 16/05/14
 * Time: 15:03
 */

namespace MintSoft\Authorize\Factory;

//use Nette\Diagnostics\Debugger;
use Zend\Cache\Storage\Adapter\Memory as MemoryCache;
use Zend\Cache\StorageFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CacheFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $configuration = $serviceLocator->get('Configuration');

        if (!isset($configuration['authorize']['cache'])) {
            return new MemoryCache();
        }

        $cacheOptions = $configuration['authorize']['cache'];
        $cacheAdapter = StorageFactory::factory($cacheOptions);

        return $cacheAdapter;
    }
}