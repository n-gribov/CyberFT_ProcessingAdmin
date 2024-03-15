<?php

use app\widgets\GridViewCustom;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\documents\documentType\DocumentTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/document-type', 'Document types');
?>

<h2><?= Html::encode($this->title) ?></h2>

<div class="filtrs">
    <a class="btn btn-sm btn-primary" id="btn-create-data" data-controller-name='document-type' href="#">
        <i class="fas fa-plus"></i> <?= Yii::t('app/document-type', 'Create document type') ?>
    </a>
</div>

<?= GridViewCustom::widget([
    'id' => 'data-table',
    'dataProvider' => $dataProvider,
    'rowOptions' => function ($model) {
        $options['onclick'] = "viewData('" . Url::toRoute(['view', 'id' => $model->primaryKey]) . "')";
        return $options;
    },
    'columns' => [
        'message_code',
        'message_name',
        [
            'attribute' => 'tariffication',
            'value' => function ($model) {
                return $model->tariffication == 1 ? Yii::t('app', 'Yes') : Yii::t('app', 'No');
            },
        ],
        [
            'attribute' => 'is_register',
            'value' => function ($model) {
                return $model->is_register == 1 ? Yii::t('app', 'Yes') : Yii::t('app', 'No');
            },
        ],
        'group_name',
        'i_date:datetime',
    ],
]); ?>

<?= $this->renderFile('@app/views/partials/_modal.php') ?>
