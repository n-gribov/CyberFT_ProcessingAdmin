<?php

use app\widgets\GridViewCustom;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\RoutingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/routing', 'Routing');

?>

<h2><?= Html::encode($this->title) ?></h2>

<div class="filtrs">
    <a class="btn btn-sm btn-primary" id="btn-create-data" data-controller-name='routing' href="#">
        <i class="fas fa-plus"></i> <?= Yii::t('app/routing', 'Create route') ?>
    </a>
</div>

<?= GridViewCustom::widget([
    'id' => 'data-table',
    'dataProvider' => $dataProvider,
    'columns' => [
        'dst_swift_code',
        'dst_member_name',
        'src_swift_code',
        'src_member_name',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{delete}',
            'buttons' => ['delete' => function ($url = '#', $model) {
                return  Html::a('<i class="far fa-trash-alt"></i>',
                    ['delete', 'id' => $model->primaryKey],
                    [
                      'data' => [
                         'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                         'method' => 'post',
                      ],
                    ]);
            }],
        ],
    ]
]); ?>

<?= $this->renderFile('@app/views/partials/_modal.php') ?>
