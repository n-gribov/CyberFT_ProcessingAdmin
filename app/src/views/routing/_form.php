<?php

use app\models\Routing;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\participants\processing\Processing;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Routing */

$cancelRoute = Url::toRoute(['/routing/view', 'id' => $model->primaryKey]);
$processings = Processing::find()->notDeleted()->orderBy('proc_name')->all();
$ownerDropDownListItems = ArrayHelper::map($processings, 'member_id', 'member_name');

?>

<div class="container-fluid document">
    <div id="edit_info">

        <?php  if ($dbError = $model->getLastDbErrorMessage()): ?>
            <div class="alert alert-danger"><?=  Html::encode($dbError) ?></div>
        <?php  endif; ?>

        <?php $form = ActiveForm::begin([
            'id' => 'data-form',
            'fieldConfig' => [
                'inputOptions' => [
                    'class' => 'form-control form-control-sm',
                    'maxlength' => true,
                ]
            ],
        ]);
        ?>

        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'dst_node')->dropDownList($ownerDropDownListItems)?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'src_node')->dropDownList($ownerDropDownListItems)?>
            </div>
        </div>

        <?php if ($model->scenario === Routing::SCENARIO_CREATE): ?>
            <?= $this->renderFile('@app/views/partials/controls/_create.php') ?>
        <?php endif; ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
