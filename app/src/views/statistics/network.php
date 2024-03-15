<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */

$this->registerJsFile('/js/statistics/network.js', ['depends' => \app\assets\ChartsAsset::class]);
$this->registerCssFile('/css/statistics/network.css');

$this->title = Yii::t('app', 'Network statistics');

?>

<div class="statistics-network">

    <h2><?= Html::encode($this->title) ?></h2>

    <h3>Processed documents per month</h3>
    <div class="row" id="documents-stats"></div>

    <hr>

    <h3>Participants</h3>
    <div id="participants-stats"></div>

    <hr>

    <h3>Terminals</h3>
    <div id="terminals-stats"></div>
</div>
