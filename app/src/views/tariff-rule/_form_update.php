<?php

use app\helpers\ViewHelper;
use app\models\participants\tariff\PrmTariffs;
use yii\helpers\Html;
use yii\helpers\Url;
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
          
        <?= $form->field($modelExt, 'intervals_amounts')->hiddenInput()->label(false) ?>    
            
        <div class="document-type">
        <div>
            <h4>
        <?php  
            if ($model->msg_type_id == -1){
                echo ($model->is_register == 0) ? 'Все документы' : 'Все реестры';
            } else {
                echo ViewHelper::getDocumentMessage($model->msg_type_id);
            }
        ?>
            </h4>
        </div>
        
<?php if ($model->is_register == 1) { ?>
        <div class="registry-tariffication">
            <div class="registry-amount-option">
            <h6>Способ расчета стоимости реестра</h6>
            <?=
                $form->field($model, 'registryAmountOption')->dropDownList(
                        [
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
<?php } ?>
            
        <div class="document-tariffication row">
            <div class="document-tariffication-list col-sm dropdown">
            <h6>Тарификация</h6>
            <!-- пока не пойму как стилизовать выпадающий список, чтобы было как в ActiveForm -->
            <?=    
                $form->field($modelExt, 'documentTarifficationOption')->dropDownList(
                    [
                        '0' => 'За документ',
                        '1' => 'За мегабайт',
                    ],
                    [
                        'onchange' => 'applyDocumentTarifficationOption(this);',            
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
        $('.registry-tariffication-rules').hide();   
        $('.document-tariffication').hide();
        document.querySelector('#prmtariffsext-intervals_amounts').disabled = true;
        
        registry_tariffication_array = [];
    }    
    
    function generatePrmTable() {
        let msg_type_id = <?= $model->msg_type_id ?>;
        let trf_id = <?= $model->trf_id ?>;
        
        $.get('/tariff-rule/generate-table-prm?msg_type_id='+msg_type_id+'&trf_id='+trf_id).done( function(response) {
            registry_tariffication_array = [];
            if (response) {
                response.forEach(function(item) {
                    registry_tariffication_array.push([item['docs_cnt_from'], item['price_sent_doc']]); 
                });
                redrawTarifficationRuleTable();                
            }   
        });
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
    
    function deleteTarifficationRuleTableRow(data) {        
        registry_tariffication_array.splice(data.dataset.number, 1);
        
        redrawTarifficationRuleTable();
    }
    
    function applyDocumentTarifficationOption(option) {
        switch (option.value) {
            case '0':
                $('.document-tariffication-amount input').attr('name', 'PrmTariffsExt[price_sent_doc]');
                $('.document-tariffication-amount input').val(<?= $modelExt->price_sent_doc ?>);
                break;
            case '1':
                $('.document-tariffication-amount input').attr('name', 'PrmTariffsExt[price_sent_mb]');
                $('.document-tariffication-amount input').val(<?= $modelExt->price_sent_mb ?>);
                break;
            default:
                break;
        }
    }

    function applyRegistryAmountOption(option) {        
        resetValues();
        
        switch (option.value) {
            case '0':
                $('.document-tariffication').show();
                $('.document-tariffication-list select').val('0');
                $('.document-tariffication-amount input').attr('name', 'PrmTariffsExt[price_sent_doc]');
        
                document.querySelector('#btn-update-data-submit').disabled = false;
                break;
            case '1':
                $('.registry-tariffication-rules').show();
                document.querySelector('#prmtariffsext-intervals_amounts').disabled = false;
                generatePrmTable();
                redrawTarifficationRuleTable();
        
                document.querySelector('#btn-update-data-submit').disabled = false;
                break;
            default:
                break;
        }
    }    
    
    $(document).ready(function () {
        resetValues();      
        
        <?php if ($model->is_register == 1 && $model->msg_type_id == -1): ?>
            $("#prmtariffs-registryamountoption").val('1');
            document.querySelector("#prmtariffs-registryamountoption").disabled = true;
            
            $('.registry-tariffication-rules').show();
            document.querySelector("#prmtariffsext-intervals_amounts").disabled = false;
            document.querySelector('#btn-update-data-submit').disabled = false;
            
            generatePrmTable();
            redrawTarifficationRuleTable();
            
        <?php elseif ($model->is_register == 1): ?>
            <?php if ($processAsIntervals == 1): ?>
                $("#prmtariffs-registryamountoption").val('1');
            <?php endif; ?>
        <?php endif; ?>
            
            
        <?php if ($model->is_register == 1): ?>
            <?php if ($model->msg_type_id == -1): ?>
                $("#prmtariffs-registryamountoption").val('1');
                document.querySelector("#prmtariffs-registryamountoption").disabled = true;  
                
                $('.registry-tariffication-rules').show();
                document.querySelector("#prmtariffsext-intervals_amounts").disabled = false;
                
                document.querySelector('#btn-update-data-submit').disabled = false;
                
                generatePrmTable();
                redrawTarifficationRuleTable();                 
                
            <?php elseif ($processAsIntervals === true): ?>
                $("#prmtariffs-registryamountoption").val('1');          
                $('.registry-tariffication-rules').show();
                
                document.querySelector("#prmtariffsext-intervals_amounts").disabled = false;
                document.querySelector('#btn-update-data-submit').disabled = false;
                
                generatePrmTable();
                redrawTarifficationRuleTable();      
                
            <?php elseif ($processAsIntervals === false): ?>                
                $("#prmtariffs-registryamountoption").val('0');   
                $('.document-tariffication').show();
                
                <?php if ($modelExt->price_sent_mb != 0 && $modelExt->price_sent_doc == 0): ?>                   
                    $('.document-tariffication-list select').val('1');
                    $('.document-tariffication-amount input').attr('name', 'PrmTariffsExt[price_sent_mb]'); 
                    $('.document-tariffication-amount input').val(<?= $modelExt->price_sent_mb ?>);
                <?php else: ?>   
                    $('.document-tariffication-list select').val('0');
                    $('.document-tariffication-amount input').attr('name', 'PrmTariffsExt[price_sent_doc]');  
                    $('.document-tariffication-amount input').val(<?= $modelExt->price_sent_doc ?>);   
                <?php endif; ?>              
                    
            <?php endif; ?>    
        <?php else: ?>   
            $('.document-tariffication').show();
            
            <?php if ($modelExt->price_sent_mb != 0 && $modelExt->price_sent_doc == 0): ?>                   
                $('.document-tariffication-list select').val('1');
                $('.document-tariffication-amount input').attr('name', 'PrmTariffsExt[price_sent_mb]'); 
                $('.document-tariffication-amount input').val(<?= $modelExt->price_sent_mb ?>);
            <?php else: ?>   
                $('.document-tariffication-list select').val('0');
                $('.document-tariffication-amount input').attr('name', 'PrmTariffsExt[price_sent_doc]');  
                $('.document-tariffication-amount input').val(<?= $modelExt->price_sent_doc ?>);   
            <?php endif; ?>
                
        <?php endif; ?>
    });   
    

</script>
