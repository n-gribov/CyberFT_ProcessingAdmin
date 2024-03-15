<?php

return [
    'traceLevel' => YII_DEBUG ? 3 : 0,
    'targets' => [
        [
            'class' => 'yii\log\FileTarget',
            'logFile' => '@runtime/logs/app-sql.log',
            'levels' => ['info', 'error', 'warning'],
            'categories' => ['yii\db\Command::query', 'yii\db\Command::execute', 'yii\db\Connection::open'],
            'logVars' => [],
        ],
        [
            'class' => 'yii\log\FileTarget',
            'levels' => ['info', 'error', 'warning'],
            'logVars' => [],
            'except' => ['yii\db\Command::query', 'yii\db\Command::execute', 'yii\db\Connection::open'],
        ],
    ],
];
