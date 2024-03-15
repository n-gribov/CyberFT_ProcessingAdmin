<?php

use app\models\documents\documentType\DocumentType;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\helpers\DocumentHelper;

/* @var $this yii\web\View */
/* @var $model app\models\documents\documentType\DocumentType */

$booleanDropDownListItems = [
    '1' => Yii::t('app', 'Yes'),
    '0' => Yii::t('app', 'No')
];

$cancelRoute = Url::toRoute(['/document-type/view', 'id' => $model->primaryKey]);

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
                <?= $form->field($model, 'message_code')->textInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'message_name')->textInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
            <?= $form->field($model, 'group_id')->dropDownList(DocumentHelper::getDocumentGroup()) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
            <?php
            if ($model->scenario === DocumentType::SCENARIO_CREATE) {
                echo $form->field($model, 'is_register')
                          ->dropDownList($booleanDropDownListItems, ['prompt' => '-']);
            } else {
                echo $form->field($model, 'is_register')
                          ->dropDownList($booleanDropDownListItems);
            }
            ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
            <?php
            if ($model->scenario === DocumentType::SCENARIO_CREATE) {
                echo $form->field($model, 'tariffication')
                          ->dropDownList($booleanDropDownListItems, ['prompt' => '-']);
            } else {
                echo $form->field($model, 'tariffication')
                          ->dropDownList($booleanDropDownListItems);
            }
            ?>
            </div>
        </div>


        <?php if ($model->scenario === DocumentType::SCENARIO_CREATE): ?>
            <?= $this->renderFile('@app/views/partials/controls/_create.php') ?>
        <?php elseif ($model->scenario === DocumentType::SCENARIO_UPDATE): ?>
            <?= $this->renderFile('@app/views/partials/controls/_update.php', compact('model', 'cancelRoute')) ?>
        <?php endif; ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
