<?php

use app\widgets\GridViewCustom;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SysParamSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/sys_param', 'System parameters');

?>

<h2 class='mb-3'><?= Html::encode($this->title) ?></h2>

<?= GridViewCustom::widget([
    'id' => 'data-table',
    'dataProvider' => $dataProvider,
    'rowOptions' => function ($model) {
        $options['onclick'] = "viewData('" . Url::toRoute(['update', 'id' => $model->primaryKey]) . "')";
        return $options;
    },
    'columns' => [
        'par_code',
        'par_descr:ntext',
        'par_value:ntext',
    ],
]); ?>

<?= $this->renderFile('@app/views/partials/_modal.php') ?>
