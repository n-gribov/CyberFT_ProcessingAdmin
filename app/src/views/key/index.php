<?php

use app\widgets\GridViewCustom;
use app\widgets\JournalSearchBox;
use yii\helpers\Html;
use yii\helpers\Url;
use app\helpers\ViewHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use app\models\participants\key\KeySearch;

/* @var $this yii\web\View */
/* @var $searchModel app\models\participants\key\KeySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $pageTitle string */

$this->title = $pageTitle;

?>

<h2><?= Html::encode($this->title) ?></h2>

<div class="filtrs d-flex justify-content-between">
    <div>
        <a class="btn btn-sm btn-primary" id="btn-create-data" data-controller-name='key' href="#">
            <i class="fas fa-plus"></i> <?= Yii::t('app/key', 'Create key') ?>
        </a>
    </div>
    <?= JournalSearchBox::widget(['searchModel' => $searchModel, 'searchUrl' => '/key']) ?>
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
        'key_code',
        'owner_name',
        [
            'attribute' => 'ownerRole',
            'label' => Yii::t('app/key', 'Owner role'),
            'filter' => KeySearch::getOwnerRoles(),
            'value' => function($model) {
                return Yii::t('app/key', $model->ownerRole);
            }
        ],
        [
            'attribute' => 'start_date',
            'format' => 'date',
            'label' => Yii::t('app/key', 'Start date'),
            'filter' => DatePicker::widget(
                array_merge(
                    [
                        'model' => $searchModel,
                        'attribute' => 'start_date',
                    ],
                    ViewHelper::defaultDatePickerConfig()
                )
            ),
        ],
        [
            'attribute' => 'end_date',
            'format' => 'date',
            'label' => Yii::t('app/key', 'End date'),
            'filter' => DatePicker::widget(
                array_merge(
                    [
                        'model' => $searchModel,
                        'attribute' => 'end_date',
                    ],
                    ViewHelper::defaultDatePickerConfig()
                )
            ),
        ],
        [
            'attribute' => 'status',
            'label' => Yii::t('app/model', 'Status'),
            'filter' => KeySearch::getKeysStatus(),
            'value' => function($model) {
                return Yii::t('app/key', $model->status_name);
            }
        ],
        [
            'attribute' => 'i_date',
            'format' => 'datetime',
            'label' => Yii::t('app/model', 'Created at'),
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
