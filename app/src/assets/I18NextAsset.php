<?php

namespace app\assets;

use yii\web\AssetBundle;

class I18NextAsset extends AssetBundle
{
    public $sourcePath = '@npm/i18next/dist/umd';
    public $js = [
        'i18next.min.js',
    ];
}
