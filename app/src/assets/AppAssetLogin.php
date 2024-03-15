<?php

namespace app\assets;

use yii\web\AssetBundle;

class AppAssetLogin extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/bootstrap.min.css',
        'css/fontawesome-all.css',
        'css/authorization.css',
    ];
}
