<?php

use app\helpers\ViewHelper;
use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \app\models\participants\key\forms\UpdateKeyForm */

$cancelRoute = Url::toRoute(['/key/view', 'id' => $model->getKeyId()]);

?>

<div class="container-fluid document">
    <div id="edit_info">

        <?php if ($dbError = $model->getLastDbErrorMessage()): ?>
            <div class="alert alert-danger"><?= Html::encode($dbError) ?></div>
        <?php endif; ?>

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
                <?= $form
                    ->field($model, 'startDate')
                    ->widget(DatePicker::class, ViewHelper::defaultDatePickerConfig())
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= $form
                    ->field($model, 'endDate')
                    ->widget(DatePicker::class, ViewHelper::defaultDatePickerConfig())
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'changeReason')->textInput() ?>
            </div>
        </div>

        <?= $this->renderFile('@app/views/partials/controls/_update.php', compact('model', 'cancelRoute')) ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
