<?php

use app\models\participants\tariff\PrmTariffs;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this View */
/* @var $model PrmTariffs */

$cancelRoute = Url::toRoute(['/tariff/update', 'id' => $backId]);

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
        <?= $form->field($model, 'trf_id')->hiddenInput(['value' => $tariffId])->label(false) ?>    
        <?= $form->field($model, 'msg_type_id')->hiddenInput(['value' => null])->label(false) ?>    
        <?= $form->field($model, 'is_register')->hiddenInput(['value' => null])->label(false) ?>    
        <?= $form->field($modelExt, 'intervals_amounts')->hiddenInput()->label(false) ?>
        <div class="document-type-list">
        <h6>Тип документа</h6>
        <?=    
            $form->field($model, 'documentTypeList')->widget(Select2::classname(), [
                            'options'       => ['placeholder' => '-'],
                            'theme' => Select2::THEME_BOOTSTRAP,
                            'pluginOptions' => [
                                'minimumInputLength' => 0,
                                'ajax'               => [
                                    'url'      => Url::to(['tariff-rule/document-type-list', 'tariff_id' => $tariffId]),
                                    'dataType' => 'json',
                                    'delay'    => 250,
                                    'data'     => new JsExpression('function(params) { return {q:params.term}; }'),
                                ],
                                'templateResult'     => new JsExpression('function(item) {
                                            return item.text;
                                        }'),
                                'templateSelection'  => new JsExpression('function(item) {
                                            return item.text;
                                        }'),
                            ],
                            'pluginEvents'  => [
                                'select2:select' => 'function(e) {
                                applyDocumentType(e.params.data)
                              }',
                                'select2:unselect' => 'function(e) {
                                
                              }'
                            ],
                        ])->label(false);    
        ?>
        </div>
        <div class="registry-tariffication">
            <div class="registry-amount-option">
            <h6>Способ расчета стоимости реестра</h6>
            <?=
                $form->field($model, 'registryAmountOption')->dropDownList(
                        [
                            null => '-',
                            0 => 'Без учета количества документов (как для обычного документа)',
                            1 => 'С учетом количества документов в реестре'
                        ],
                        [
                            'onchange' => 'applyRegistryAmountOption(this);'
                        ]
                )->label(false);       
            ?>
            </div>
        </div>
        <div class="document-tariffication row">
            <div class="document-tariffication-list col-sm dropdown">
            <h6>Тарификация</h6>
            <?=    
                $form->field($modelExt, 'documentTarifficationOption')->dropDownList(
                    [
                        '0' => 'За документ',
                        '1' => 'За мегабайт',
                    ],
                    [
                        'onchange' => 'applyDocumentTarifficationOption(this);'    
                    ]
                )->label(false);
            ?>
            </div>
            <div class="document-tariffication-amount col-sm">
            <h6>Стоимость</h6>
            <?=
                $form->field($modelExt, 'price_sent_doc')
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
                                        'class' => 'form-control form-control-sm'
                                    ]
                                ])->label(false);
            ?>
            </div>
        </div>
        <div class="registry-tariffication-rules">    
            <div class="registry-tariffication-rules-table">

                
            </div>    

            <div class="registry-tariffication-rules-add-block">
                <table class="table">
                    <tr>
                        <th>Минимальное количество документов</th>
                        <th>Стоимость</th>
                        <th></th>
                    </tr>
                    <tr>
                        <td>
                            <?= 
                            $form->field($modelExt, 'doc_min')
                                    ->widget(
                                        MaskedInput::className(), [
                                            'clientOptions' => [
                                                'alias' => 'integer',
                                                'allowMinus' => false,
                                                'min' => '1'
                                            ],
                                            'options' => [
                                                'class' => 'form-control form-control-sm'
                                            ]
                                        ])->label(false);
                        ?>
                        <td>
                            <?= 
                            $form->field($modelExt, 'amount')
                                    ->widget(
                                        MaskedInput::className(), [
                                            'clientOptions' => [
                                                'alias' => 'decimal',
                                                'digits' => 2,
                                                'digitsOptional' => false,
                                                'radixPoint' => '.',
                                                'groupSeparator' => ' ',
                                                'autoGroup' => true,
                                                'autoUnmask' => true,
                                            ],
                                            'options' => [
                                                'class' => 'form-control form-control-sm'
                                            ]
                                        ])->label(false);
                        ?>
                        </td>
                        <td><button type="button" id="btn-create-data-submit" class="btn btn-primary btn-sm btn-block" 
                                                    onclick="addRegistryTarifficationRule()">Применить</button>
                        </td>
                    </tr>
                </table>

            </div>
        </div>
            <?= $this->renderFile('@app/views/partials/controls/_update.php', compact('model', 'cancelRoute')) ?>


        <?php ActiveForm::end(); ?>
        
    </div>
</div>

<script>    
    function resetValues() {
        $('.registry-tariffication').hide();
        $('.document-tariffication').hide();         
        $('.registry-tariffication-rules').hide();  
        
        registry_tariffication_array = [];
        
        document.querySelector("#prmtariffs-registryamountoption").disabled = false;
        document.querySelector("#prmtariffsext-intervals_amounts").disabled = true;
        document.querySelector('#btn-update-data-submit').disabled = true;
    }

    function redrawTarifficationRuleTable() {
        let out_table = '<table class="table"><tr><th>Количество документов</th><th>Стоимость за документ</th><th><th></tr>';
        
        registry_tariffication_array.sort( function(a, b) { return a[0] - b[0]; } );
        
        $('#prmtariffsext-intervals_amounts').val( JSON.stringify(registry_tariffication_array) );
        
        let interval_text;
        
        for(let i = 0; i < registry_tariffication_array.length; i++){
            if (i !== registry_tariffication_array.length - 1){
                interval_text = 'От '+registry_tariffication_array[i][0]+' до '+(registry_tariffication_array[i+1][0] - 1);
            } else {
                interval_text = 'От '+registry_tariffication_array[i][0];
            }
            out_table += '<tr><td>'+interval_text+'</td>';
            out_table += '<td>'+rubFormat.format(registry_tariffication_array[i][1])+'</td>';
            out_table += '<td><a href="#" data-number='+i+' onclick="deleteTarifficationRuleTableRow(this)"><i class="fas fa-trash"></i></a></td></tr>';
        }        
        
        out_table += '</table>';
        
        $('.registry-tariffication-rules-table').html(out_table);
    }
    
    function deleteTarifficationRuleTableRow(data) {        
        registry_tariffication_array.splice(data.dataset.number, 1);
        
        redrawTarifficationRuleTable();
    }
    
    function applyDocumentType(documentType) {
        resetValues();
    
        $('.registry-amount-option select').val(null);
        $('.document-tariffication-amount input').val('');
        
        let msg_type_id = ( $('.document-type-list select').val() < 0 ) ? '-1' : $('.document-type-list select').val();
        
        $('#prmtariffs-msg_type_id').val( msg_type_id );
    
        switch (documentType.is_register) {
            case 0:
                $('.document-tariffication').show();
                $('.document-tariffication-list select').val('0');
                $('.document-tariffication-amount input').attr('name', 'PrmTariffsExt[price_sent_doc]');
                $('#prmtariffs-is_register').val('0');
                document.querySelector('#btn-update-data-submit').disabled = false;
                break;
            case 1:
                $('.registry-tariffication').show();
                $('#prmtariffs-is_register').val('1');
                // Если мы выбрали "Все реестры", то расчет ведем только для интервалов
                if (msg_type_id == -1) {
                    $("#prmtariffs-registryamountoption").val('1');
                    document.querySelector("#prmtariffs-registryamountoption").disabled = true;
                    $('.registry-tariffication-rules').show();
                    document.querySelector("#prmtariffsext-intervals_amounts").disabled = false;
                    document.querySelector('#btn-update-data-submit').disabled = false;
                    redrawTarifficationRuleTable();
                }
                break;
            default:
                break;
        }
    }

    function applyRegistryAmountOption(option) {
        resetValues();
        
        $('.registry-tariffication').show();
        
        switch (option.value) {
            case '0':
                $('.document-tariffication').show();
                $('.document-tariffication-list select').val('0');
                $('.document-tariffication-amount input').attr('name', 'PrmTariffsExt[price_sent_doc]');
                document.querySelector('#btn-update-data-submit').disabled = false;
                break;
            case '1':
                $('.registry-tariffication-rules').show();
                document.querySelector("#prmtariffsext-intervals_amounts").disabled = false;
                redrawTarifficationRuleTable();
                document.querySelector('#btn-update-data-submit').disabled = false;
                break;
            default:
                document.querySelector('#btn-update-data-submit').disabled = true;
                break;
        }
    }
    
    function applyDocumentTarifficationOption(option) {
        switch (option.value) {
            case '0':
                $('.document-tariffication-amount input').attr('name', 'PrmTariffsExt[price_sent_doc]');
                break;
            case '1':
                $('.document-tariffication-amount input').attr('name', 'PrmTariffsExt[price_sent_mb]');
                break;
            default:
                break;
        }
    }
    
     
    function addRegistryTarifficationRule() {
        
        let min = $('#prmtariffsext-doc_min').val();
        let amount = $('#prmtariffsext-amount').val();
        
        //Перебор на случай обновления стоимости для введенного минимального количества документов        
        for(let i = 0; i < registry_tariffication_array.length; i++){
            if (registry_tariffication_array[i][0] == min) {
                registry_tariffication_array[i][1] = amount; 
                redrawTarifficationRuleTable();
                return;
            }            
        }
        registry_tariffication_array.push([min, amount]); 
        redrawTarifficationRuleTable();
    }
    
    $(document).ready(function () {
        resetValues();
        
    });   
</script>
