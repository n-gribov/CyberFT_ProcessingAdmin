<?php

use app\helpers\ViewHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */

$moneyFormat = new NumberFormatter("ru", NumberFormatter::CURRENCY);

?>
<?php if ((isset($dataProvider)) && (count($dataProvider->getModels()) !== 0)): ?>
<table class="table">
    <thead>
    <tr>
        <th><?= Yii::t('app/tariff', 'Document type') ?></th>
        <th><?= Yii::t('app/tariff', 'Documents count in registry') ?></th>
        <th><?= Yii::t('app/tariff', 'Price per document') ?></th>
        <th><?= Yii::t('app/tariff', 'Price per MB') ?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
<?php
    $duplicateRows = 0;
    foreach( $dataProvider->getModels() as $model_item ){
?>
    <tr>
<?php
        if ($duplicateRows == 0) {
            $count = $arrayCount[$model_item['trf_prm_id']];
            if ($count == 1) {
?>
        <td>
<?php 
        if (!$model_item['message_name']){
            echo ($model_item['is_register'] == 0) ? 'Прочие документы' : 'Прочие реестры';
        } else {
            echo $model_item['message_name']; 
        }
?>
        </td>
<?php
            } else {
?>
        <td rowspan="<?= $count ?>">
<?php 
        if (!$model_item['message_name']){
            echo ($model_item['is_register'] == 0) ? 'Прочие документы' : 'Прочие реестры';
        } else {
            echo $model_item['message_name']; 
        }
?>
        </td>
<?php
                
            }
        } 
?>
        <td>
            <?= ViewHelper::getDocumentsCountInterval($model_item['docs_cnt_from'], $model_item['docs_cnt_to']) ?>
        </td>
        <td>
            <?= ($model_item['price_sent_doc'] != 0) ? 
                                    $moneyFormat->formatCurrency($model_item['price_sent_doc'], 'RUB')
                                    : '-' ?>
        </td>        
        <td>
            <?= ($model_item['price_sent_mb'] != 0) ? 
                                    $moneyFormat->formatCurrency($model_item['price_sent_mb'], 'RUB') 
                                    : '-' ?>
        </td>
<?php
        if ($duplicateRows == 0) {            
            $count = $arrayCount[$model_item['trf_prm_id']];
            if ($count == 1) {
?>
        <td>
<?=
    Html::a(
        '<i class="fas fa-edit"></i> ', 
        '#',
        [
            'class' => 'text-right text-primary',
            'onclick' => "viewData('" . Url::toRoute(['tariff-rule/update', 
                    'id' => $model_item['trf_prm_id'],
                    'extId' => $model_item['trf_prm_ext_id'],
                    'tariffId' => $model->tariff_id
                    ]) . "')"
        ]
    )
?>
<?=
    Html::a(
        '<i class="fas fa-trash"></i> ', 
        '#',
        [
            'class' => 'text-right text-primary',
            'onclick' => "viewData('" . Url::toRoute(['tariff-rule/delete', 
                    'id' => $model_item['trf_prm_id']
                    ]) . "')"
        ]
    )
?>
        </td>   
<?php
            } else {
                $duplicateRows = $count;
?>
        <td rowspan="<?= $count ?>">
<?=
    Html::a(
        '<i class="fas fa-edit"></i> ', 
        '#',
        [
            'class' => 'text-right text-primary',
            'onclick' => "viewData('" . Url::toRoute(['tariff-rule/update',                 
                    'id' => $model_item['trf_prm_id'],
                    'extId' => $model_item['trf_prm_ext_id'],
                    'tariffId' => $model->tariff_id                
                    ]) . "')"
        ]
    )
?>
<?=
    Html::a(
        '<i class="fas fa-trash"></i> ', 
        '#',
        [
            'class' => 'text-right text-primary',
            'onclick' => "viewData('" . Url::toRoute(['tariff-rule/delete', 
                    'id' => $model_item['trf_prm_id']
                    ]) . "')"
        ]
    )
?>
        </td>
<?php
                $duplicateRows--;
            }
        } else {
            $duplicateRows--;
        }
?>
    </tr>
<?php
    }
?>
    </tbody>
</table>   
<?php else: ?>
<br/>
<?php endif; ?>
<?=
    Html::a(
        Yii::t('app/tariff', 'Add rule') . ' <i class="fas fa-plus"></i> ', 
        '#',
        [
            'class' => 'text-right text-primary',
            'style' => 'font-size: 18px',
            'onclick' => "viewData('" . Url::toRoute(['tariff-rule/create', 'id' => $model->tariff_id]) . "')"
        ]
    )
?>            
<div class="row mt-3">
    <div class="col-12">
        <button id="btn-update-data-cancel" type="button" class="btn btn-secondary btn-sm btn-block"
                data-route="<?=$cancelRoute?>">
            <?=Yii::t('app', 'Back')?>
        </button>
    </div>
</div>