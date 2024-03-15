<?php

use app\widgets\GridViewCustom;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/user', 'Users');

?>

<h2><?= Html::encode($this->title) ?></h2>

<div class="filtrs">
    <a class="btn btn-sm btn-primary" id="btn-create-data" data-controller-name='user' href="#">
        <i class="fas fa-user-plus"></i> <?= Yii::t('app/user', 'Create user') ?>
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
        'user_id',
        'user_code',
        [
            'attribute' => 'status',
            'value' => function($model) {
                return $model->status ? Yii::t('app/user', 'Active') : Yii::t('app/user', 'Deleted');
            }
        ],
        'user_name',
        'comm:ntext',
        'e_mail',
    ],
]); ?>


<?=$this->renderFile('@app/views/partials/_modal.php')?>