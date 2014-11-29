<?php

return [
    'factories' => [
        'MintSoft\Authorize'                  => 'MintSoft\Authorize\Factory\AuthorizeFactory',
        'MintSoft\Authorize\ControllerGuard'  => 'MintSoft\Authorize\Factory\ControllerGuardFactory',
        'MintSoft\Authorize\AuthorizeBuilder' => 'MintSoft\Authorize\Factory\AuthorizeBuilderFactory',
        'MintSoft\Authorize\RoleProvider'     => 'MintSoft\Authorize\RoleProvider',
        'MintSoft\Authorize\MvcListener'      => 'MintSoft\Authorize\Factory\AuthorizeListenerFactory',
        'MintSoft\Authorize\Cache'            => 'MintSoft\Authorize\Factory\CacheFactory',
    ],
];