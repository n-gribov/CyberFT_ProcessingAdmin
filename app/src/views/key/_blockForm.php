<?php

use app\helpers\ViewHelper;
use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

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
                <?= $form->field($model, 'blockInfo')->textInput() ?>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-9 pr-0">
                <button id="btn-update-data-submit" type="submit" class="btn btn-danger btn-sm btn-block">
                    <?= Yii::t('app/key', 'Block')?>
                </button>
            </div>
            <div class="col-3">
                <button id="btn-update-data-cancel" type="button" class="btn btn-default btn-sm btn-block"
                        data-route="<?=$cancelRoute?>">
                    <?=Yii::t('app', 'Cancel')?>
                </button>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
