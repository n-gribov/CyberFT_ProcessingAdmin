<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/bootstrap.min.css',
        'css/fontawesome-all.css',
        'css/style.css',
        'css/custom.css'
    ];

    public $js = [
        'js/popper.min.js',
        'js/bootstrap.min.js',
        'js/script.js',
        'js/custom.js',
        'js/crud.js',
        'js/i18n.js',
    ];

    public $depends = [
        YiiAsset::class,
        I18NextAsset::class,
    ];
}
