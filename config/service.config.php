<?php

return [
    'factories' => [
        'MintSoft\Authorize\Rbac'                    => 'MintSoft\Authorize\Factory\RbacFactory',
        'MintSoft\Authorize\MvcKeeper'               => 'MintSoft\Authorize\Factory\MvcKeeperFactory',
        'MintSoft\Authorize\Cache'                   => 'MintSoft\Authorize\Factory\CacheFactory',
        'MintSoft\Authorize\Annotation\Builder'      => 'MintSoft\Authorize\Factory\BuilderFactory',
        'MintSoft\Authorize\Provider\Permission'     => 'MintSoft\Authorize\Provider\Permission\PermissionProvider',
        'MintSoft\Authorize\Provider\Role'           => 'MintSoft\Authorize\Provider\Role\RoleProvider',
        'MintSoft\Authorize\AccessForbiddenStrategy' => 'MintSoft\Authorize\Factory\AccessForbiddenFactory',
    ],
];