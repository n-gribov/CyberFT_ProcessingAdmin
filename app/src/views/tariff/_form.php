<?php

use app\models\participants\tariff\MemberTariff;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this View */
/* @var $model MemberTariff */

$cancelRoute = Url::toRoute(['/participant/view', 'id' => $participantId]);

echo $this->render('@app/views/tariff/_alert');
?>

<div class="container-fluid document">
    <div id="edit_info">

        <?php  if ($dbError = $model->getLastDbErrorMessage()): ?>
            <div class="alert alert-danger"><?=  Html::encode($dbError) ?></div>
        <?php  endif; ?>
        <?php 
            if ($model->scenario === MemberTariff::SCENARIO_UPDATE) {
                $formParams = [
                    'id' => 'data-form',
                    'action' => ['tariff/update', 'id' => $model->memb_trf_id],
                    'fieldConfig' => [
                        'inputOptions' => [
                            'class' => 'form-control form-control-sm',
                            'maxlength' => true,
                        ]
                    ],
                ];
            } else if ($model->scenario === MemberTariff::SCENARIO_CREATE) {
                $formParams = [
                    'id' => 'data-form',
                    'fieldConfig' => [
                        'inputOptions' => [
                            'class' => 'form-control form-control-sm',
                            'maxlength' => true,
                        ]
                    ],
                ];              
            }
            
        ?>
        <?php $form = ActiveForm::begin($formParams);
        ?>

        <div class="row align-items-end">
        <?php if ($model->scenario === MemberTariff::SCENARIO_CREATE): ?>
            <div class="col-12">
            <?= $form->field($model, 'fix_pay', ['template' => '{label}{input}'])
                            ->widget(MaskedInput::className(), [
                                'clientOptions' => [
                                    'alias' => 'decimal',
                                    'digits' => 2,
                                    'digitsOptional' => false,
                                    'radixPoint' => '.',
                                    'groupSeparator' => ' ',
                                    'autoGroup' => true,
                                    'autoUnmask' => true,
                                    'removeMaskOnSubmit' => true,
                                ],
                                'options' => [
                                    'style' => 'width: 100%; padding: 0.275rem;'                                    
                                ]
                            ]); ?>
                
            </div>  
        <?php elseif ($model->scenario === MemberTariff::SCENARIO_UPDATE): ?>            
            <div class="col-6">
            <?= $form->field($model, 'fix_pay')
                            ->widget(MaskedInput::className(), [
                                'clientOptions' => [
                                    'alias' => 'decimal',
                                    'digits' => 2,
                                    'digitsOptional' => false,
                                    'radixPoint' => '.',
                                    'groupSeparator' => ' ',
                                    'autoGroup' => true,
                                    'autoUnmask' => true,
                                    'removeMaskOnSubmit' => true,
                                ],
                                'options' => [
                                    'style' => 'width: 100%; padding: 0.275rem;'                                    
                                ]
                            ]); ?>
                
            </div>     
            <div class="col-6 ">
                <button type="button" id="btn-fix-pay-change" class="btn btn-primary btn-sm btn-block" 
                                                    >Изменить</button>
                <button type="submit" id="btn-fix-pay-save" class="btn btn-primary btn-sm btn-block" 
                                                    disabled style="display: none;">Сохранить</button>
            </div>
        <?php endif; ?>
        </div>            
        <?= $form->field($model, 'member_id')->hiddenInput(['value' => $participantId])->label(false); ?>
        <?= $form->field($model, 'tariff_id')->hiddenInput(['value' => $model->tariff_id])->label(false); ?>                  
            
        <?php if ($model->scenario === MemberTariff::SCENARIO_CREATE): ?>
        <div class="row mt-3">
            <div class="col-9">
                <button type="submit" id="btn-create-data-submit" class="btn btn-primary btn-sm btn-block">
                    <?=Yii::t('app', 'Create')?>
                </button>
            </div>
            <div class="col-3">
                <button id="btn-update-data-cancel" type="button" class="btn btn-secondary btn-sm btn-block"
                        data-route="<?=$cancelRoute?>">
                    <?=Yii::t('app', 'Cancel')?>
                </button>
            </div>   
        </div>
        <?php endif; ?>

        <?php ActiveForm::end(); ?>
            
        <?php if ($model->scenario === MemberTariff::SCENARIO_UPDATE): ?>
        <?=
            $this->renderFile('@app/views/tariff/_prm_grid.php', compact('model', 'dataProvider', 'arrayCount', 'cancelRoute'))                       
        ?>  
        <?php endif; ?>
        
    </div>
</div>

<script>
    
    $('#btn-fix-pay-change').click(function (){
        document.querySelector('#membertariff-fix_pay').disabled = false;
        document.querySelector('#btn-fix-pay-save').disabled = false;
        document.querySelector('#btn-fix-pay-save').style = '';
        document.querySelector('#btn-fix-pay-change').disabled = true;      
        document.querySelector('#btn-fix-pay-change').style = 'display: none;';            
    });
    
    <?php if ($model->scenario === MemberTariff::SCENARIO_UPDATE): ?>
    $(document).ready(function () {
        document.querySelector('#membertariff-fix_pay').disabled = true;
    });  
    <?php endif; ?>
        
</script>