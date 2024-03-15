<?php

use app\models\participants\terminal\Terminal;
use app\widgets\GridViewCustom;
use app\widgets\JournalSearchBox;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\participants\terminal\TerminalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$request = Yii::$app->request;
$id = $searchModel->member_id != null ? $searchModel->member_id : FALSE;
$this->title = $pageTitle;

?>

<h2><?= Html::encode($this->title) ?></h2>

<div class="filtrs d-flex justify-content-between">
    <div>
        <?= Html::a(
            '<i class="fas fa-plus"></i> '.Yii::t('app/terminal', 'Create terminal'),
            '#',
            [
                'id' => 'btn-create-data',
                'data-controller-name' => 'terminal',
                'class' => 'btn btn-sm btn-primary',
                'data-parent-id' => $id,
            ])
        ?>
    </div>
    <?= JournalSearchBox::widget(['searchModel' => $searchModel, 'searchUrl' => '/terminal']) ?>
</div>

<?= GridViewCustom::widget([
    'id' => 'data-table',
    'dataProvider' => $dataProvider,
    'rowOptions' => function ($model) {
        $options['onclick'] = "viewData('" . Url::toRoute(['view', 'id' => $model->primaryKey]) . "')";
        return $options;
    },
    'columns' => [
        'terminal_name',
        'terminal_code',
        'full_swift_code',
        'member_name',
        'member_swift_code',
        'status_name:ntext',
        'blocked:boolean',
        'i_date:datetime',
    ],
]); ?>

<?= $this->renderFile('@app/views/partials/_modal.php') ?>
