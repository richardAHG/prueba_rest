<?php

$config = [
    'id' => 'pruebas_back',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [],
    'modules' => require __DIR__ . '/modules.php',
    'aliases' => [
        '@bower' => '@vendor/bower-asset'
    ],
    'language' => 'es-ES',
    'timeZone' => 'America/Lima',
    'components' => [
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
        'request' => [
            'cookieValidationKey' => 'pruebas_back',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser'
            ]
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'db' => require __DIR__ . '/db.php',
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => require __DIR__ . '/routes.php',
        ]
    ],
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config["modules"]["gii"] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
