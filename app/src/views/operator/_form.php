<?php

use app\helpers\ViewHelper;
use app\models\participants\operator\Operator;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\participants\operator\Operator */
/* @var $processings \app\models\participants\processing\Processing[] */
/* @var $participants \app\models\participants\participant\Participant[] */
/* @var $terminals \app\models\participants\terminal\Terminal[] */
/* @var $roles \app\models\participants\operator\OperatorRole[] */

$roleDropDownItems = ArrayHelper::map($roles, 'role_id', 'role_name');
$terminalDropDownItems = ArrayHelper::map($terminals, 'terminal_id', 'terminal_name');

$participantDropDownListItems = [];
if (!empty($processings)){
    $participantDropDownListItems[Yii::t('app/terminal', 'Processings')] = ArrayHelper::map($processings, 'member_id', 'member_name');
}
if (!empty($participants)){
    $participantDropDownListItems[Yii::t('app/terminal', 'Participants')] = ArrayHelper::map($participants, 'member_id', 'member_name');
}

$cancelRoute = Url::toRoute(['/operator/view', 'id' => $model->primaryKey]);

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

        <?php if ($model->scenario === Operator::SCENARIO_CREATE): ?>
            <div class="row">
                <div class="col-12">
                    <?= $form->field($model, 'member_id')
                        ->widget(
                            Select2::class,
                            array_merge(
                                [
                                    'attribute' => 'ownerId',
                                    'data' => $participantDropDownListItems,
                                    'pluginEvents' => [
                                        'select2:select' => 'updateTerminalSelectOptions',
                                    ],
                                ],
                                ViewHelper::defaultSelect2Config()
                            )
                        )
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                <?php if (count($terminalDropDownItems) === 1) {
                          echo $form->field($model, 'terminal_id')
                                    ->dropDownList($terminalDropDownItems);
                      } else {
                          echo $form->field($model, 'terminal_id')
                                    ->dropDownList($terminalDropDownItems, ['prompt' => '-']);
                      }
                ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'operator_name')->textInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'role_id')
                    ->dropDownList($roleDropDownItems, ['prompt' => '-']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'operator_position')->textInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'phone')->textInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'email')->textInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'external_code')->textInput() ?>
            </div>
        </div>

        <?php if ($model->scenario === Operator::SCENARIO_CREATE): ?>
            <?= $this->renderFile('@app/views/partials/controls/_create.php') ?>
        <?php elseif ($model->scenario === Operator::SCENARIO_UPDATE): ?>
            <?= $this->renderFile('@app/views/partials/controls/_update.php', compact('model', 'cancelRoute')) ?>
        <?php endif; ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
