<?php
use MintSoft\Authorize\Module as AuthorizeModule;
use Zend\Loader\AutoloaderFactory as ZendAutoloader;

include __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../src/MintSoft/Authorize/Module.php';

ZendAutoloader::factory((new AuthorizeModule())->getAutoloaderConfig());

