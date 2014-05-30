<?php
return [
    'authorize'    => [
        'cache' => [
//            'adapter' => [
//                'name'    => 'filesystem',
//                'options' => [
//                    'cache_dir'       => 'data/authorize',
//                    'dir_permission'  => 0755,
//                    'file_permission' => 0666,
//                    'namespace'       => 'mint-soft-authorize'
//                ],
//            ],
            'adapter' => [
                'name' => 'memory',
            ],
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'error/403' => __DIR__ . '/../src/MintSoft/Authorize/view/error/403.phtml',
        ]
    ],
];