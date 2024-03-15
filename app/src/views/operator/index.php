<?php

use app\widgets\GridViewCustom;
use app\widgets\JournalSearchBox;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\participants\operator\OperatorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$request = Yii::$app->request;
$id = $searchModel->member_id != null ? $searchModel->member_id : FALSE;
$this->title = $pageTitle;

?>

<h2><?= Html::encode($this->title) ?></h2>

<div class="filtrs d-flex justify-content-between">
    <div>
        <?= Html::a(
            '<i class="fas fa-plus"></i> '.Yii::t('app/operator', 'Create operator'),
            '#',
            [
                'id' => 'btn-create-data',
                'data-controller-name' => 'operator',
                'class' => 'btn btn-sm btn-primary',
                'data-participant-id' => $id,
            ])
        ?>
    </div>
    <?= JournalSearchBox::widget(['searchModel' => $searchModel, 'searchUrl' => '/operator']) ?>
</div>

<?= GridViewCustom::widget([
    'id' => 'data-table',
    'dataProvider' => $dataProvider,
    'rowOptions' => function ($model) {
        $options['onclick'] = "viewData('" . Url::toRoute(['view', 'id' => $model->primaryKey]) . "')";
        return $options;
    },
    'columns' => [
        'operator_name',
        'role_name',
        'member_name',
        'swift_code',
        'terminal_name',
        'full_swift_code',
        'status_name',
        'blocked:boolean',
        'i_date:datetime',
        'operator_position',
        'phone',
        'email:email',
    ],
]); ?>

<?= $this->renderFile('@app/views/partials/_modal.php') ?>

<?php $this->registerJsFile('/js/operator/index.js', ['depends' => \yii\web\JqueryAsset::class]);
