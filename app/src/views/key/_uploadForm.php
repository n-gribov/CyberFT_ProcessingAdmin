<?php

use app\helpers\ViewHelper;
use app\models\participants\operator\Operator;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\participants\key\forms\UpdateKeyForm */
/* @var $operators Operator[] */

$ownerDropDownItems = array_reduce(
    $operators,
    function (array $carry, Operator $operator) {
        $carry[$operator->member_name][$operator->operator_id] = "$operator->operator_name ($operator->role_name)";
        return $carry;
    },
    []
);

?>

<div class="container-fluid document">
    <div id="edit_info">
        <?php $form = ActiveForm::begin([
            'id' => 'data-form',
            'action' => Url::to('/key/upload'),
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
                    ->field($model, 'ownerId')
                    ->widget(
                        Select2::class,
                        array_merge(
                            [
                                'attribute' => 'message_code',
                                'data' => $ownerDropDownItems,
                            ],
                            ViewHelper::defaultSelect2Config()
                        )
                    ) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'keyBodyFile')->fileInput() ?>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <button id="btn-update-data-submit" type="submit" class="btn btn-primary btn-sm btn-block">
                    <?= Yii::t('app', 'Next')?>
                </button>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
