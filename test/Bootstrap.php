<?php
/**  @var $loader \Composer\Autoload\ClassLoader */
$loader = include __DIR__ . '/../vendor/autoload.php';
$loader->set('MintSoft\\Authorize\\', [__DIR__ . '/../module/src']);