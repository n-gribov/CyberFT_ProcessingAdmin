<?php

use app\helpers\ViewHelper;
use app\models\participants\member\MemberStatus;
use app\models\participants\processing\Processing;
use app\models\participants\processing\ProcessingSearch;
use app\widgets\GridViewCustom;
use app\widgets\JournalSearchBox;
use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel ProcessingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/participant', 'Processings');

?>

<h2><?= Html::encode($this->title) ?></h2>

<div class="filtrs d-flex justify-content-between">
    <div>
        <a class="btn btn-sm btn-primary" id="btn-create-data" data-controller-name="processing" href="#">
            <i class="fas fa-plus"></i> <?= Yii::t('app/participant', 'Create processing') ?>
        </a>
    </div>
    <?= JournalSearchBox::widget(['searchModel' => $searchModel, 'searchUrl' => '/processing']) ?>
</div>

<?= GridViewCustom::widget([
    'id' => 'data-table',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'rowOptions' => function ($model) {
        $options['onclick'] = "viewData('" . Url::toRoute(['view', 'id' => $model->primaryKey]) . "')";
        return $options;
    },
    'columns' => [
        'member_name',
        'swift_code',
        'is_primary:boolean',
        [
            'attribute' => 'is_bank',
            'format' => 'boolean',
            'label' => Yii::t('app/participant', 'Credit organization')
        ],
        [
            'attribute' => 'status',
            'value' => function (Processing $model) {
                return $model->status_name;
            },
            'filter' => MemberStatus::getStatusFilterOptions(),
        ],
        'blocked:boolean',
        [
            'attribute' => 'i_date',
            'format' => 'datetime',
            'filter' => DatePicker::widget(
                array_merge(
                    [
                        'model' => $searchModel,
                        'attribute' => 'i_date',
                    ],
                    ViewHelper::defaultDatePickerConfig()
                )
            ),
        ],
    ],
]); ?>

<?= $this->renderFile('@app/views/partials/_modal.php') ?>
