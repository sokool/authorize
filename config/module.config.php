<?php
return [
    'authorize'    => [
        'cache' => [
//            'adapter' => [
//                'name'    => 'filesystem',
//                'options' => [
//                    'cache_dir'       => '/tmp/mint-soft/authorize',
//                    'dirPermission'  => 0777,
//                    'filePermission' => 0666,
//                    'dirLevel' => 0,
//                    'namespace'       => 'mint-soft-authorize'
//                ],
//            ],
            'adapter' => [
                'name' => 'memory',
            ],
        ],
    ],
    'view_manager' => [
        'template_map'   => [
            'error/403' => __DIR__ . '/../src/MintSoft/Authorize/view/error/403.phtml',
        ],
        'mvc_strategies' => [
            'MintSoft\Authorize\MvcListener',
        ],
    ],
];