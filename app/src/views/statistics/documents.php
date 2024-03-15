<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $deliveryStatusStats \app\models\documents\statistics\DocumentsDeliveryStatistics[] */
/* @var $participantsStatsDays integer */

$this->registerJsFile('/js/statistics/documents.js', ['depends' => \app\assets\ChartsAsset::class]);
$this->registerCssFile('/css/statistics/documents.css');

$this->title = Yii::t('app', 'Documents exchange statistics');

?>

<div class="statistics-documents">

    <h2><?= Html::encode($this->title) ?></h2>

    <h3>Delivery status</h3>
    <div class="row" id="documents-stats">
        <?php foreach ($deliveryStatusStats as $stats): ?>
            <div class="col-3">
                <div class="category-header"><?= $stats->getPeriodLabel() ?></div>
                <ul class="list-unstyled">
                    <li>
                        <i class="fa fa-circle delivered"></i>
                        Delivered: <?= $stats->deliveredCount ?>
                    </li>
                    <li>
                        <i class="fa fa-circle pending"></i>
                        Pending: <?= $stats->pendingCount ?>
                    </li>
                    <li>
                        <i class="fa fa-circle failed"></i>
                        Failed: <?= $stats->failedCount ?>
                    </li>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>

    <hr>

    <h3>Top senders</h3>
    <p class="sub-header">Last <?= $participantsStatsDays ?> days</p>
    <div id="senders-stats"></div>

    <hr>

    <h3>Top receivers</h3>
    <p class="sub-header">Last <?= $participantsStatsDays ?> days</p>
    <div id="receivers-stats"></div>
</div>
