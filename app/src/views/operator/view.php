<?php

use app\widgets\DetailViewCustom;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\participants\operator\Operator */

$addChildButtonText = Yii::t('app/operator', 'Add key');
$addChildButtonUrl = Url::to(['/key', '#' => "create({$model->primaryKey})"]);

?>

<div class="container-fluid document">
    <nav class="nav nav-pills nav-justified">
        <?= Html::a(
            '<i class="fas fa-fw fa-key"></i>' . Yii::t('app/key', 'Keys'),
            ['/key', 'KeySearch[owner_id]' => $model->operator_id],
            ['class' => 'btn btn-sm btn-outline-primary']
        ) ?>
    </nav>
    <div id="show_info">
        <div class="tab-pane fade show active" id="show_info" role="tabpanel">
            <?= DetailViewCustom::widget([
                'model' => $model,
                'attributes' => [
                    'operator_id',
                    'operator_name',
                    'role_name',
                    'operator_position',
                    'email:email',
                    'phone',

                    'member_name',
                    'swift_code',
                    'terminal_name',
                    'full_swift_code',

                    'status_name',
                    'blocked:boolean',
                    'block_info',

                    'full_privs:boolean',
                    'external_code',

                    'i_date:datetime',
                    'i_user',
                    'u_date:datetime',
                    'u_user',
                ],
                'attributesGroups' => [
                    [
                        'attributes' => [
                            'operator_id',
                            'operator_name',
                            'role_name',
                            'operator_position',
                            'email',
                            'phone',
                        ],
                    ],
                    [
                        'header' => Yii::t('app/model', 'Owner'),
                        'attributes' => [
                            'member_name',
                            'swift_code',
                            'terminal_name',
                            'full_swift_code',
                        ],
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
                            'full_privs',
                            'external_code',
                        ],
                        'collapsed' => true,
                    ],
                    [
                        'header' => Yii::t('app', 'Audit'),
                        'attributes' => [
                            'i_date',
                            'i_user',
                            'u_date',
                            'u_user',
                        ],
                        'collapsed' => true,
                    ],
                ]
            ]) ?>

            <?= $this->renderFile(
                '@app/views/partials/controls/_view.php',
                compact('model', 'addChildButtonUrl', 'addChildButtonText')
            ) ?>
        </div>
    </div>
</div>
