<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$log = require __DIR__ . '/log.php';
$i18n = require __DIR__ . '/i18n.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gon'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language' => env('DEFAULT_LANGUAGE', 'ru'),
    'components' => [
        'request' => [
            'cookieValidationKey' => env('COOKIE_VALIDATION_KEY'),
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'i18n' => $i18n,
        'log' => $log,
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'formatter' => [
            'class' => 'app\i18n\Formatter',
            'dateFormat' => 'dd.MM.yyyy',
            'datetimeFormat' => 'dd.MM.yyyy HH:mm',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'nullDisplay' => '',
            'timeZone' => 'Europe/Moscow',
            'defaultTimeZone' => 'Europe/Moscow',
        ],
        'gon' => 'app\components\GonComponent',
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
        'generators' => [
            'crud' => [
                'class' => 'app\gii\generators\crud\Generator',
            ],
            'advancedModel' => [
                'class' => 'app\gii\generators\model\Generator',
            ],
        ],
    ];
}

return $config;
