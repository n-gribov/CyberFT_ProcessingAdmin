<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => env('DB_DATA_SOURCE'),
    'charset' => 'utf8',
    'enableLogging' => YII_DEBUG,
];
