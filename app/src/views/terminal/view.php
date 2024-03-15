<?php

use app\models\participants\terminal\Terminal;
use app\widgets\DetailViewCustom;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\participants\terminal\Terminal */

$addChildButtonText = Yii::t('app/terminal', 'Add operator');
$addChildButtonUrl = Url::to(['/operator', '#' => "create({$model->primaryKey})"]);

?>

<div class="container-fluid document">
    <div id="show_info">
        <div class="tab-pane fade show active" id="show_info" role="tabpanel">
            <?= DetailViewCustom::widget([
                'model' => $model,
                'attributes' => [
                    'terminal_id',
                    'terminal_name',
                    'terminal_code',
                    'full_swift_code',

                    'member_name',
                    'member_swift_code',

                    'status_name',
                    'blocked:boolean',
                    'block_info',
                    [
                        'attribute' => 'login_param',
                        'contentOptions' => ['style' => 'word-break: break-word']
                    ],
                    'login_addr',
                    'work_mode_name',
                    'fragment_ready:boolean',
                    [
                        'attribute' => 'fragment_size',
                        'value' => $model->fragment_size ?: Yii::t('app', '(no limit)'),
                    ],
                    [
                        'attribute' => 'queue_length',
                        'value' => $model->queue_length ?: Yii::t('app', '(no limit)'),
                    ],

                    'i_date:datetime',
                    'i_user',
                    'u_date:datetime',
                    'u_user',
                    'd_date:datetime',
                    'd_user',
                ],
                'attributesGroups' => [
                    [
                        'attributes' => [
                            'terminal_id',
                            'terminal_name',
                            'terminal_code',
                            'full_swift_code',
                        ],
                    ],
                    [
                        'header' => Yii::t('app/model', 'Owner'),
                        'attributes' => ['member_name', 'member_swift_code'],
                    ],
                    [
                        'header' => Yii::t('app/model', 'Status'),
                        'attributes' => [
                            'status_name',
                            'blocked',
                            'block_info',
                        ],
                    ],
                    [
                        'header' => Yii::t('app', 'Settings'),
                        'attributes' => [
                            'login_param',
                            'login_addr',
                            'work_mode_name',
                            'fragment_ready',
                            'fragment_size',
                            'queue_length',
                        ],
                    ],
                    [
                        'header' => Yii::t('app', 'Audit'),
                        'attributes' => [
                            'i_date',
                            'i_user',
                            'u_date',
                            'u_user',
                            'd_date',
                            'd_user',
                        ],
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
                <?php if ($addChildButtonUrl && $addChildButtonText): ?>
                    <div class="pl-1">
                        <?= Html::a(
                            '<i class="fas fa-plus"></i> ' . $addChildButtonText,
                            $addChildButtonUrl,
                            ['class' => 'btn btn-primary btn-sm btn-block']
                        ) ?>
                    </div>
                <?php endif; ?>

                <?php if ($model->blocked && $model->status !== Terminal::STATUS_DELETED): ?>
                    <div class="pl-1">
                        <?= Html::a(
                            '<i class="fas fa-unlock"></i> ' . Yii::t('app', 'Unblock'),
                            '#',
                            [
                                'id' => 'unblock-data',
                                'class' => 'btn btn-primary btn-sm float-right',
                                'data' => ['id' => $model->primaryKey]
                            ]
                        ); ?>
                    </div>
                <?php endif; ?>

                <?php if (!$model->blocked && $model->status !== Terminal::STATUS_DELETED): ?>
                    <div class="ml-auto">
                        <?= Html::a(
                            '<i class="fas fa-lock"></i> ' . Yii::t('app', 'Block'),
                            '#',
                            [
                                'id' => 'block-data',
                                'class' => 'btn btn-danger btn-sm float-right',
                                'data' => ['id' => $model->primaryKey]
                            ]
                        ); ?>
                    </div>
                <?php endif; ?>

                <?php if ($model->blocked): ?>
                    <div class="ml-auto">
                        <?= Html::a('<i class="far fa-trash-alt"></i> ' . Yii::t('app', 'Delete'),
                            ['delete', 'id' => $model->primaryKey],
                            [
                                'class' => 'btn btn-danger btn-sm float-right',
                                'data' => [
                                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                    'method' => 'post',
                                ],
                            ]
                        ) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
