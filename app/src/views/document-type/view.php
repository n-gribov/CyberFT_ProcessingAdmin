<?php

use app\widgets\DetailViewCustom;

/* @var $this yii\web\View */
/* @var $model app\models\documents\documentType\DocumentType */

?>

<div class="container-fluid document">
    <div id="show_info">
        <p class="font-weight-bold mt-1 mb-2"><?= Yii::t('app/document-type', 'General information')?>:</p>
        <hr>
        <div class="tab-pane fade show active" id="show_info" role="tabpanel">
            <?= DetailViewCustom::widget([
                'model' => $model,
                'attributes' => [
                    'message_code',
                    'message_name',
                    'group_name',
                    'system_name',
                    'tariffication:boolean',
                    'is_register:boolean',
                ],
        ]) ?>
        </div>
        <p class="font-weight-bold mt-3 mb-2"><?= Yii::t('app/document-type', 'Audit')?>:</p>
        <hr>
        <div class="tab-pane fade show active" id="show_info" role="tabpanel">
            <?= DetailViewCustom::widget([
                'model' => $model,
                'attributes' => [
                    'i_date:datetime',
                    'i_user',
                    'u_date:datetime',
                    'u_user',
                    //'d_date:datetime',
                    //'d_user',
                ],
            ]) ?>
            <?= $this->renderFile('@app/views/partials/controls/_view.php', compact('model')) ?>
        </div>
    </div>
</div>
