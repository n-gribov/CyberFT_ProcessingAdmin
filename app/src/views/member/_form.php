<?php

use app\models\participants\processing\Processing;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use app\helpers\ViewHelper;

/** @var $this yii\web\View */
/** @var $model app\models\participants\member\Member */
/** @var $processings Processing[] */
/** @var $countries \app\models\dictionaries\country\Country[] */
/** @var $languages \app\models\dictionaries\language\Language[] */

$isProcessing = $model instanceof Processing;


$cancelRoute = Url::toRoute(["/{$this->context->id}/view", 'id' => $model->primaryKey]);

$processsingDropDownListItems = ArrayHelper::map($processings, 'member_id', 'member_name');
$countryDropDownListItems = ArrayHelper::map($countries, 'cntr_code2', 'cntr_name');

$booleanDropDownListItems = [
    '1' => Yii::t('app', 'Yes'),
    '0' => Yii::t('app', 'No')
];

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

        <div class="row">
            <div class="col-6">
                <?php if ($isProcessing): ?>
                    <?= $form->field($model, 'is_primary')
                        ->dropDownList($booleanDropDownListItems, ['prompt' => '-']);
                    ?>
                <?php else: ?>
                    <?= $form->field($model, 'parent_id')
                        ->dropDownList($processsingDropDownListItems, ['prompt' => '-'])
                        ->label(Yii::t('app/participant', 'Processing'))
                    ?>
                <?php endif; ?>
            </div>
            <div class="col-6">
                <?= $form->field($model, 'cntr_code2')
                         ->widget(
                             Select2::class,
                             array_merge(
                                 [
                                     'attribute' => 'cntr_code2',
                                     'data' => $countryDropDownListItems,
                                 ],
                                 ViewHelper::defaultSelect2Config()
                             )
                         )
                         ->label(Yii::t('app/participant', 'Country'))
                ?>
            </div>
            <div class="col-6">
                <?= $form->field($model, 'swift_code')->textInput(['maxlength' => '11']) ?>
            </div>
            <div class="col-6">
                <?= $form->field($model, 'city_name')->textInput() ?>
            </div>
            <div class="col-6">
                <?= $form->field($model, 'member_phone')->textInput() ?>
            </div>
            <div class="col-6">
                <?= $form->field($model, 'member_name')->textInput() ?>
            </div>
            <div class="col-6">
                <?= $form->field($model, 'website')->textInput() ?>
            </div>
            <div class="col-6">
                <?= $form->field($model, 'eng_name')->textInput() ?>
            </div>
            <div class="col-6">
                <?= $form->field($model, 'is_bank')
                         ->dropDownList($booleanDropDownListItems, ['prompt' => '-']) ?>
            </div>
            <?php if ($isProcessing): ?>
                <div class="col-6">
                    <?= $form->field($model, 'api_url')->textInput() ?>
                </div>
            <?php endif; ?>
            <div class="col-12">
                <?= $form->field($model, 'registr_info')->textarea(['rows' => 6]) ?>
            </div>
        </div>

        <?php if ($model->scenario == \app\models\participants\participant\Participant::SCENARIO_CREATE): ?>
            <?= $this->renderFile('@app/views/partials/controls/_create.php') ?>
        <?php elseif ($model->scenario == \app\models\participants\participant\Participant::SCENARIO_UPDATE): ?>
            <?= $this->renderFile('@app/views/partials/controls/_update.php', compact('model', 'cancelRoute')) ?>
        <?php endif; ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
