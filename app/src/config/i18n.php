<?php

return [
    'translations' => [
        'app*' => [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@app/messages',
            'fileMap' => [
                'app' => 'app.php',
                'app/model' => 'model.php',
                'app/user' => 'user.php',
                'app/document' => 'document.php',
                'app/participant' => 'participant.php',
                'app/tariff' => 'tariff.php',
                'app/terminal' => 'terminal.php',
                'app/operator' => 'operator.php',
                'app/key' => 'key.php',
                'app/document-status' => 'document-status.php',
                'app/document-type' => 'document-type.php',
                'app/routing' => 'routing.php',
                'app/sys_param' => 'sys_param.php',
                'app/role' => 'role.php',
            ],
        ],
    ]
];
