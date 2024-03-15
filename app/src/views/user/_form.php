<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\helpers\UserHelper;
use app\models\User;
use yii\helpers\BaseHtml;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */

$sendMailDropDownList = [
    '0' => Yii::t('app', 'No'),
    '1' => Yii::t('app', 'Yes')
];

$accountStatusDropDownList = [
    '0' => Yii::t('app/user', 'Blocked'),
    '1' => Yii::t('app/user', 'Active')
];

$classFieldOptions = ['class' => 'form-control form-control-sm'];

$simpleFieldOptions = $classFieldOptions;
$simpleFieldOptions['maxlength'] = true;

$textareaFieldOptions = $classFieldOptions;
$textareaFieldOptions['rows'] = 6;

$cancelRoute = Url::toRoute(['/user/view', 'id' => $model->getPrimaryKey()]);

?>

<div class="container-fluid document">
    <div id="edit_info">
        <?php if ($dbError = $model->getLastDbErrorMessage()): ?>
            <div class="alert alert-danger"><?=$dbError?></div>
        <?php endif; ?>

        <?php $form = ActiveForm::begin(['id' => 'data-form']); ?>

        <?php if ($model->scenario == User::SCENARIO_CREATE): ?>
            <div class="row">
                <div class="col-6">
                    <?= $form->field($model, 'user_code')->textInput($simpleFieldOptions) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'user_name')->textInput($simpleFieldOptions) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <?= $form->field($model, 'password')->passwordInput($simpleFieldOptions) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 're_password')->passwordInput($simpleFieldOptions) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <?= $form->field($model, 'e_mail')->input('email', $classFieldOptions) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'send_mail')->dropDownList($sendMailDropDownList, $classFieldOptions) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?= $form->field($model, 'role')
                             ->checkboxList(UserHelper::getUserRoles(), ['class' => 'list-group'])
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?= $form->field($model, 'comm')->textarea($textareaFieldOptions) ?>
                </div>
            </div>
            <?=$this->renderFile('@app/views/partials/controls/_create.php')?>
        <?php endif; ?>

        <?php if ($model->scenario == User::SCENARIO_UPDATE): ?>
            <div class="row">
                <div class="col-6">
                    <?= $form->field($model, 'user_name')->textInput($simpleFieldOptions) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'e_mail')->input('email', $classFieldOptions) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <?= $form->field($model, 'account_status')->dropDownList($accountStatusDropDownList, $classFieldOptions) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'send_mail')->dropDownList($sendMailDropDownList, $classFieldOptions) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <?= $form->field($model, 'password')
                             ->passwordInput($simpleFieldOptions)
                             ->label(Yii::t('app/user', 'New password'))
                    ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 're_password')
                             ->passwordInput($simpleFieldOptions)
                             ->label(Yii::t('app/user', 'Repeat new password'))
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?= $form->field($model, 'role')
                             ->checkboxList(UserHelper::getUserRoles(), ['class' => 'list-group'])
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?= $form->field($model, 'comm')->textarea($textareaFieldOptions) ?>
                </div>
            </div>
            <?=$this->renderFile('@app/views/partials/controls/_update.php', compact('model', 'cancelRoute'))?>
        <?php endif; ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
