<?php

use app\helpers\ViewHelper;
use app\models\participants\terminal\Terminal;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\participants\terminal\Terminal */
/* @var $form yii\widgets\ActiveForm */
/* @var $processings \app\models\participants\processing\Processing[] */
/* @var $participants \app\models\participants\participant\Participant[] */
/* @var $workModes array */

$ownerDropDownListItems = [];
if (!empty($processings)){
    $ownerDropDownListItems[Yii::t('app/terminal', 'Processings')] = ArrayHelper::map($processings, 'member_id', 'member_name');
}
if (!empty($participants)){
    $ownerDropDownListItems[Yii::t('app/terminal', 'Participants')] = ArrayHelper::map($participants, 'member_id', 'member_name');
}

$fragmentationSupportDropDownListItems = [
    '1' => Yii::t('app', 'Yes'),
    '0' => Yii::t('app', 'No'),
];

$cancelRoute = Url::toRoute(['/terminal/view', 'id' => $model->primaryKey]);

?>

<div class="container-fluid document">
    <div id="edit_info">

        <?php if ($dbError = $model->getLastDbErrorMessage()): ?>
            <div class="alert alert-danger"><?= Html::encode($dbError) ?></div>
        <?php endif; ?>

        <?php
        $form = ActiveForm::begin([
            'id' => 'data-form',
            'fieldConfig' => [
                'inputOptions' => [
                    'class' => 'form-control form-control-sm',
                    'maxlength' => true,
                ]
            ],
        ]);
        ?>
        <?php if ($model->scenario == Terminal::SCENARIO_CREATE): ?>
            <div class="row">
                <div class="col-12">
                    <?= $form->field($model, 'member_id')
                        ->widget(
                            Select2::class,
                            array_merge(
                                [
                                    'attribute' => 'ownerId',
                                    'data' => $ownerDropDownListItems,
                                ],
                                ViewHelper::defaultSelect2Config()
                            )
                        )->label(Yii::t('app/model', 'Owner'))
                    ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'terminal_name')->textInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'terminal_code')->textInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'work_mode')->dropDownList($workModes) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'login_param')->textInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'login_addr')->textInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'fragment_ready')->dropDownList($fragmentationSupportDropDownListItems) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'fragment_size')->textInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'queue_length')->textInput() ?>
            </div>
        </div>

        <?php if ($model->scenario == Terminal::SCENARIO_CREATE): ?>
            <?= $this->renderFile('@app/views/partials/controls/_create.php') ?>
        <?php elseif ($model->scenario == Terminal::SCENARIO_UPDATE): ?>
            <?= $this->renderFile('@app/views/partials/controls/_update.php', compact('model', 'cancelRoute')) ?>
        <?php endif; ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
