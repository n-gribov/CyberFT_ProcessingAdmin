<?php

use app\models\participants\key\Key;
use app\widgets\DetailViewCustom;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\participants\key\Key */

$blockInfoLabel = $model->status == Key::STATUS_BLOCKED ? Yii::t('app', 'Block reason') : Yii::t('app/key', 'Unblock reason');

$bUserLabel = $model->status == Key::STATUS_BLOCKED ? Yii::t('app', 'Blocked') : Yii::t('app', 'Unblocked');

$auditAttributes = ['i_user', 'u_user', 'change_info', 'b_user', 'block_info'];

$isNotNullAttribute = function ($a) use ($model) {
    return $model->$a != null;
};

$auditAttributes = array_filter($auditAttributes, $isNotNullAttribute);

?>

<div class="container-fluid document">
    <div id="show_info">
        <div class="tab-pane fade show active" id="show_info" role="tabpanel">
            <?= DetailViewCustom::widget([
                'model' => $model,
                'attributes' => [
                    'key_id',
                    'key_code',
                    'owner_name',
                    [
                        'attribute' => 'key_body',
                        'format' => 'raw',
                        'value' => function (Key $key) {
                            if ($key->key_body) {
                                return Html::a(Yii::t('app', 'Download file'), ['download-key-file', 'id' => $key->key_id]);
                            }
                            return null;
                        }
                    ],

                    'status_name',
                    'start_date:date',
                    'end_date:date',
                    [
                        'attribute' => 'block_info',
                        'label' => $blockInfoLabel,
                    ],


                    [
                        'attribute' => 'i_user',
                        'label' => Yii::t('app', 'Created'),
                        'value' => function ($model) {
                            return $model->i_user.' '.Yii::$app->formatter->asDateTime($model->i_date);
                        }
                    ],
                    [
                        'attribute' => 'u_user',
                        'label' => Yii::t('app', 'Updated'),
                        'value' => function ($model) {
                            return $model->u_user.' '.Yii::$app->formatter->asDateTime($model->u_date);
                        }
                    ],
                    [
                        'attribute' => 'b_user',
                        'label' => $bUserLabel,
                        'value' => function ($model) {
                            return $model->b_user.' '.Yii::$app->formatter->asDateTime($model->b_date);
                        }
                    ],
                    'change_info',
                ],
                'attributesGroups' => [
                    [
                        'attributes' => [
                            'key_id',
                            'key_code',
                            'owner_name',
                            'key_body'
                        ],
                    ],
                    [
                        'attributes' => [
                            'status_name',
                            'start_date',
                            'end_date',
                        ],
                    ],
                    [
                        'header' => Yii::t('app', 'Audit'),
                        'attributes' => $auditAttributes,
                        'collapsed' => true,
                    ],
                ]
            ]) ?>

            <div class="d-flex mt-3">
                <div>
                    <?= Html::a(
                        '<i class="fas fa-edit"></i> ' . Yii::t('app', 'Update'),
                        '#',
                        [
                            'id' => 'update-data',
                            'class' => 'btn btn-primary btn-sm btn-block',
                            'data' => ['id' => $model->primaryKey]
                        ]
                    ) ?>
                </div>
                <?php
                if ($model->status == Key::STATUS_ACTIVE) {
                    echo Html::beginTag('div', ['class' => 'ml-auto']);
                    echo Html::a(
                        '<i class="fas fa-lock"></i> ' . Yii::t('app', 'Block'),
                        '#',
                        [
                            'id' => 'block-data',
                            'class' => 'btn btn-danger btn-sm float-right',
                            'data' => ['id' => $model->primaryKey]
                        ]
                    );
                    echo Html::endTag('div');
                } elseif ($model->status == Key::STATUS_BLOCKED) {
                    echo Html::beginTag('div', ['class' => 'pl-1']);
                    echo Html::a(
                        '<i class="fas fa-unlock"></i> ' . Yii::t('app', 'Unblock'),
                        '#',
                        [
                            'id' => 'unblock-data',
                            'class' => 'btn btn-primary btn-sm float-right',
                            'data' => ['id' => $model->primaryKey]
                        ]
                    );
                    echo Html::endTag('div');
                } elseif ($model->status == Key::STATUS_AWAITING_ACTIVATION) {
                    echo Html::beginTag('div', ['class' => 'pl-1']);
                    echo Html::a(
                             '<i class="fas fa-check"></i> '.Yii::t('app/key', 'Activate'),
                             ['activate', 'id' => $model->primaryKey],
                             ['class' => 'btn btn-primary btn-sm btn-block']
                         );
                    echo Html::endTag('div');
                }

                if ($model->status == Key::STATUS_BLOCKED or $model->status == Key::STATUS_AWAITING_ACTIVATION) {
                    echo Html::beginTag('div', ['class' => 'ml-auto']);
                    echo Html::a('<i class="far fa-trash-alt"></i> ' . Yii::t('app', 'Delete'),
                             ['delete', 'id' => $model->primaryKey],
                             [
                                 'class' => 'btn btn-danger btn-sm float-right',
                                 'data' => [
                                     'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                     'method' => 'post',
                                 ],
                             ]
                         );
                    echo Html::endTag('div');
                }
                ?>
            </div>
        </div>
    </div>
</div>
