<?php

/* @var $this yii\web\View */
/* @var $model app\models\User */

use app\widgets\DetailViewCustom;
use yii\helpers\Html;

$roles = array_column($model->executeGetRoles(), 'role_name');

?>

<div class="container-fluid document">
    <div id="show_info">
        <div class="tab-pane fade show active" id="show_info" role="tabpanel">
            <?=DetailViewCustom::widget([
                'model' => $model,
                'attributes' => [
                    'user_id',
                    'user_code',
                    [
                        'attribute' => 'status',
                        'value' => $model->status ? Yii::t('app/user', 'Active') : Yii::t('app/user', 'Deleted')
                    ],
                    'user_name',
                    'comm',
                    'i_date:datetime',
                    'd_date:datetime',
                    'i_user',
                    'd_user',
                    [
                        'attribute' => 'send_mail',
                        'value' => $model->send_mail ? Yii::t('app', 'Yes') : Yii::t('app', 'No')
                    ],
                    'e_mail',
                    [
                        'attribute' => 'account_status',
                        'value' => $model->account_status ? Yii::t('app/user', 'Active') : Yii::t('app/user', 'Blocked')
                    ],
                    [
                        'attribute' => 'role',
                        'value' => implode(Html::tag('br') ,$roles),
                        'format' => 'html',
                    ],
                ],
            ]) ?>
            <?=$this->renderFile('@app/views/partials/controls/_view.php', compact('model'))?>
        </div>
    </div>
</div>
