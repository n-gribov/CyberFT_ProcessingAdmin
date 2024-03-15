<?php

use app\models\SysParam;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\widgets\DetailViewCustom;

/* @var $this yii\web\View */
/* @var $model app\models\SysParam */

// $cancelRoute = Url::toRoute(['/sys-param/view', 'id' => $model->primaryKey]);

?>

<div class="container-fluid document">
    <div id="show_info">

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
            <div class="col-6">
                <?= Html::tag('p', Html::encode(Yii::t('app/sys_param', 'Code').':')) ?>
            </div>
            <div class="col-6">
                <?= Html::tag('p', Html::encode($model->par_code), ['class' => 'font-weight-bold']) ?>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-6">
                <?= Html::tag('p', Html::encode(Yii::t('app/sys_param', 'Description').':')) ?>
            </div>
            <div class="col-6">
                <?= Html::tag('p', Html::encode($model->par_descr), ['class' => 'font-weight-bold']) ?>
            </div>
        </div>
        <hr>
        <?php if ($model->is_modifiable == true): ?>
            <div class="row">
                <div class="col-12">
                    <?php if ($model->par_type == $model::PAR_TYPE_BOOL): ?>
                        <?= $form->field($model, 'par_value')
                                 ->dropDownList(['1' => Yii::t('app', 'Yes'), '0' => Yii::t('app', 'No')]);?>
                    <?php else: ?>
                        <?= $form->field($model, 'par_value')->textInput();?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-9 pr-0">
                    <?= Html::submitButton(Yii::t('app', 'Save'), [
                            'class' => 'btn btn-primary btn-sm btn-block',
                            'id' => 'btn-update-data-submit'
                            ]);?>
                </div>
                <div class="col-3">
                    <?= Html::submitButton(Yii::t('app', 'Cancel'), [
                            'class' => 'btn btn-default btn-sm btn-block',
                            'id' => 'btn-update-data-cancel',
                            'data-dismiss' => 'modal',
                            'aria-label' => 'Close'
                            ]);?>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-6">
                    <?= Html::tag('p', Html::encode(Yii::t('app/sys_param', 'Value').' '.
                                                    Yii::t('app/sys_param', '(not editable)').':'));?>
                </div>
                <div class="col-6">
                    <?= Html::tag('p', Html::encode($model->par_value), ['class' => 'font-weight-bold']);?>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <?= Html::submitButton(Yii::t('app', 'Cancel'), [
                            'class' => 'btn btn-default btn-sm btn-block',
                            'id' => 'btn-update-data-cancel',
                            'data-dismiss' => 'modal',
                            'aria-label' => 'Close'
                            ]);?>
                </div>
            </div>
        <?php endif; ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
