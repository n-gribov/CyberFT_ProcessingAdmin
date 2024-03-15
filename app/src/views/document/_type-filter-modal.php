<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var array $data */
/** @var \app\models\documents\document\DocumentSearch $searchModel */

?>

<div class="modal fade" id="type-filter-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app', 'Document type') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= Select2::widget([
                    'id'        => 'type-select',
                    'name'      => 'select',
                    'data'      => $data,
                    'options'   => [
                        'autocomplete' => 'off',
                        'placeholder'  => '',
                        'multiple'     => true,
                    ],
                ])
                ?>
                <input type="hidden" name="state" value="<?= Html::encode(json_encode($searchModel->message_codes ?: [])) ?>"/>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-primary clear-button"><?= Yii::t('app', 'Clear') ?></button>
                <button class="btn btn-sm btn-primary apply-button"><?= Yii::t('app', 'Apply') ?></button>
            </div>
        </div>
    </div>
</div>
