<?php
return [
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'area' => 'v1/area',
        ],
        'pluralize' => false,
        // 'except' => ['delete', 'create', 'update', 'view'],
        'prefix' => '/v1.0/<proyecto_id:\\d>',
        'extraPatterns' => [
            'POST ' => 'create',
            'PUT {id}' => 'update',
            // 'GET {area_id}/usuario' => 'usuarioIndex',
        ],
        'tokens' => [
            '{id}' => '<id:\\d+>',
            '{area_id}' => '<area_id:\\d+>',
        ],
    ],
];
