<?php

use app\models\participants\member\Member;
use app\models\participants\member\MemberStatus;
use app\models\participants\tariff\MemberTariff;
use app\models\participants\processing\Processing;
use app\widgets\DetailViewCustom;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $model Member */

$addChildButtonText = Yii::t('app/participant', 'Add terminal');
$addChildButtonUrl = Url::to(['/terminal', '#' => "create({$model->primaryKey})"]);
$isProcessing = $model instanceof Processing;
$isEditable = $isProcessing || $model->isLocal;

$auditAttributes = array_filter(
    ['i_user', 'u_user', 'd_user', 'block_info'],
    function ($attribute) use ($model) {
        return $model->$attribute !== null && $model->$attribute !== '';
    }
);

?>

<div class="container-fluid document">
    <nav class="nav nav-pills nav-justified">
        <?php
        if ($isEditable) {
              echo Html::a(
                  '<i class="fas fa-fw fa-desktop mr-1"></i>'.Yii::t('app/participant', 'Terminals'),
                  ['/terminal', 'TerminalSearch[member_id]' => $model->member_id],
                  ['class' => 'btn btn-sm btn-outline-primary']
              );
              echo Html::a(
                  '<i class="fas fa-fw fa-address-card mr-1"></i>'.Yii::t('app/participant', 'Operators'),
                  ['/operator', 'OperatorSearch[member_id]' => $model->member_id],
                  ['class' => 'btn btn-sm btn-outline-primary']
              );
        }
        if (!$isProcessing) {
            echo Html::a(
                '<i class="fas fa-fw fa-exchange-alt mr-1"></i>'.Yii::t('app/participant', 'Participant exchange list'),
                ['/participant', 'ParticipantSearch[partnerMemberSwiftCode]' => $model->swift_code],
                ['class' => 'btn btn-sm btn-outline-primary']
            );
        }
        ?>
    </nav>
    <div id="show_info">
        <div class="tab-pane fade show active" id="show_info" role="tabpanel">
            <?= DetailViewCustom::widget([
                'model' => $model,
                'attributes' => [
                    'member_id',
                    'member_name',
                    'swift_code',
                    'eng_name',
                    [
                        'attribute' => 'is_primary',
                        'format' => 'boolean',
                        'visible' => $isProcessing,
                    ],
                    'is_bank:boolean',
                    'registr_info:ntext',

                    'proc_name',
                    'proc_swift_code',

                    'status_name',
                    'valid_from:date',
                    'valid_to:date',
                    'blocked:boolean',
                    'block_info',

                    'cntr_name',
                    'city_name',
                    'website',
                    'member_phone',

                    'lang_name',
                    'auto_modify:boolean',

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
                        'attribute' => 'd_user',
                        'label' => Yii::t('app', 'Deleted'),
                        'value' => function ($model) {
                            return $model->d_user.' '.Yii::$app->formatter->asDateTime($model->d_date);
                        }
                    ],
                    'block_info',

                    [
                        'attribute' => 'tariff_plan',
                        'format' => 'raw',
                        'value' => function (Member $model) {
                            try {
                                $memberTariff = MemberTariff::find()
                                    ->where(['member_id' => $model->member_id, 'amnd_state' => 1, 'status' => 1])
                                    ->one();

                                return $memberTariff
                                    ? Html::a(
                                        '<i class="fas fa-edit"></i> ' . Yii::t('app', 'Update'),
                                        '#',
                                        [
                                            'class'   => 'btn btn-primary btn-sm btn-block',
                                            'onclick' => "viewData('" . Url::toRoute(['tariff/update', 'id' => $memberTariff->memb_trf_id]) . "')"
                                        ]
                                    )
                                    : Html::a(
                                        '<i class="fas fa-plus"></i> ' . Yii::t('app', 'Create'),
                                        '#',
                                        [
                                            'class'   => 'btn btn-primary btn-sm btn-block',
                                            'onclick' => "viewData('" . Url::toRoute(['tariff/create', 'participantId' => $model->member_id]) . "')"
                                        ]
                                    );
                            } catch (\Exception $exception) {
                                Yii::$app->errorHandler->logException($exception);
                                return '';
                            }
                        },
                        'visible' => !$isProcessing,
                    ],
                    [
                        'attribute' => 'api_url',
                        'value' => function (Member $model) {
                            return $model instanceof Processing
                                ? $model->api_url
                                : null;
                        },
                        'visible' => $isProcessing,
                    ],
                ],
                'attributesGroups' => [
                    [
                        'attributes' => [
                            'member_id',
                            'member_name',
                            'swift_code',
                            'eng_name',
                            'is_primary',
                            'is_bank',
                            'registr_info',
                            'tariff_plan',
                            'api_url',
                        ],
                    ],
                    [
                        'header' => Yii::t('app/participant', 'Processing'),
                        'attributes' => ['proc_name', 'proc_swift_code'],
                        'visible' => !$isProcessing,
                    ],
                    [
                        'header' => Yii::t('app/model', 'Status'),
                        'attributes' => [
                            'status_name',
                            'valid_from',
                            'valid_to',
                            'blocked',
                            'block_info',
                        ],
                        'collapsed' => true,
                    ],
                    [
                        'header' => Yii::t('app/participant', 'Address and contacts'),
                        'attributes' => [
                            'cntr_name',
                            'city_name',
                            'member_phone',
                            'website',
                        ],
                        'collapsed' => true,
                    ],
                    [
                        'header' => Yii::t('app', 'Settings'),
                        'attributes' => ['lang_name', 'auto_modify'],
                        'collapsed' => true,
                    ],
                    [
                        'header' => Yii::t('app', 'Audit'),
                        'attributes' => $auditAttributes,
                        'collapsed' => true,
                    ],
                ]
            ]) ?>

            <div class="d-flex mt-3">
                <?php if ($isEditable): ?>
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
                    <div class="pl-1">
                        <?= Html::a(
                            '<i class="fas fa-plus"></i> ' . $addChildButtonText,
                            $addChildButtonUrl,
                            ['class' => 'btn btn-primary btn-sm btn-block']
                        ) ?>
                    </div>
                <?php endif; ?>
                <?php if ($model->blocked && $model->status !== MemberStatus::STATUS_DELETED): ?>
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

                <?php if (!$model->blocked && $model->status !== MemberStatus::STATUS_DELETED): ?>
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
                <?php if ($isEditable && $model->blocked): ?>
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
