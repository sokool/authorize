<?php

return [
    'factories' => [
        'Authorize\Rbac'                         => 'Authorize\Factory\RbacFactory',
        'Authorize\MvcKeeper'                    => 'Authorize\Factory\MvcKeeperFactory',
        'Authorize\Cache'                        => 'Authorize\Factory\CacheFactory',
        'Authorize\Annotation\Builder'           => 'Authorize\Factory\BuilderFactory',
        'Authorize\Provider\Permission'          => '',
        'Authorize\Provider\Role'                => '',
        'Authorize\View\AccessForbiddenStrategy' => 'Authorize\Factory\AccessForbiddenFactory',
    ],
];