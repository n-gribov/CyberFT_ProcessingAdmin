<?php

namespace app\assets;

use yii\web\AssetBundle;

class ChartsAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/Chart.bundle.min.js',
        'js/charts.js',
        'js/charts-colors.js',
    ];

    public $depends = [
        AppAsset::class
    ];
}
