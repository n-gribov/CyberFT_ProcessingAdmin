<?php

use app\models\documents\document\Document;
use app\widgets\DetailViewCustom;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model Document */

?>

<div class="container-fluid document">
    <div id="show_info">
        <?= $this->render('@app/views/partials/_alert', ['baseClasses' => []])?>
        <div class="tab-pane fade show active" id="show_info" role="tabpanel">
            <?= DetailViewCustom::widget([
                'model' => $model,
                'attributes' => [
                   'message_id',
                    [
                        'attribute' => 'message_name',
                        'value' => function (Document $document) {
                            return Yii::t('app/document-type', $document->message_name);
                        }
                    ],
                    'message_code',
                    [
                        'attribute' => 'sender_name',
                        'label' => Yii::t('app/document', 'Participant'),
                    ],
                    [
                        'attribute' => 'sender_swift_code',
                        'label' => Yii::t('app/document', 'Participant code'),
                    ],
                    [
                        'attribute' => 'snd_terminal_name',
                        'label' => Yii::t('app/document', 'Terminal'),
                    ],
                    [
                        'attribute' => 'snd_full_swift_code',
                        'label' => Yii::t('app/document', 'Terminal code'),
                    ],
                    [
                        'attribute' => 'receiver_name',
                        'label' => Yii::t('app/document', 'Participant'),
                    ],
                    [
                        'attribute' => 'receiver_swift_code',
                        'label' => Yii::t('app/document', 'Participant code'),
                    ],
                    [
                        'attribute' => 'rsv_terminal_name',
                        'label' => Yii::t('app/document', 'Terminal'),
                    ],
                    [
                        'attribute' => 'rsv_full_swift_code',
                        'label' => Yii::t('app/document', 'Terminal code'),
                    ],
                    [
                        'attribute' => 'send_status_name',
                        'value' => function (Document $document) {
                            return Yii::t('app/document-status', $document->send_status_name);
                        }
                    ],
                    'reg_time:datetime',
                    'finish_time:datetime',
                    'final_time:datetime',
                    [
                        'attribute' => 'err_msg',
                        'label' => Yii::t('app/document', 'Error'),
                        'value' => function (Document $model) {
                            return $model->is_error ? "{$model->err_code}, {$model->err_msg}" : Yii::t('app', 'No');
                        },
                    ],
                    [
                        'attribute' => 'sender_msg_code',
                        'label' => Yii::t('app/document', 'Sender message identifier'),
                    ],
                    'message_length',
                    'message_hash',
                    'message_sum:currency',
                ],
                'attributesGroups' => [
                    [
                        'attributes' => ['message_id', 'message_name', 'message_code'],
                    ],
                    [
                        'header' => Yii::t('app/document', 'Sender'),
                        'attributes' => ['sender_name', 'sender_swift_code', 'snd_terminal_name', 'snd_full_swift_code'],
                    ],
                    [
                        'header' => Yii::t('app/document', 'Receiver'),
                        'attributes' => ['receiver_name', 'receiver_swift_code', 'rsv_terminal_name', 'rsv_full_swift_code'],
                    ],
                    [
                        'header' => Yii::t('app/document', 'Transport information'),
                        'attributes' => ['send_status_name', 'reg_time', 'finish_time', 'final_time', 'err_msg', 'sender_msg_code', 'message_length'],
                    ],
                    [
                        'header' => Yii::t('app/document', 'Additional information'),
                        'attributes' => ['message_hash', 'message_sum'],
                    ],
                ]
            ]) ?>
            <div class="d-flex flex-row flex-row-reverse mt-3">
                <?php
                if ($model->isSuspendable()) {
                    echo Html::a(
                        '<i class="fa fa-ban"></i> ' . Yii::t('app/document', 'Suspend delivery'),
                        ['suspend-delivery', 'id' => $model->primaryKey],
                        ['class' => 'btn btn-danger btn-sm modal-action-button']
                    );
                }
                if ($model->isResendable()) {
                    echo Html::a(
                        '<i class="fa fa-paper-plane"></i> ' . Yii::t('app/document', 'Resend'),
                        ['resend', 'id' => $model->primaryKey],
                        ['class' => 'btn btn-primary btn-sm modal-action-button']
                    );
                }
                ?>
            </div>
        </div>
    </div>
</div>
