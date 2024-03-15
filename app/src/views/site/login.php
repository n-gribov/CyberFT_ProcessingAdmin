<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', 'Authorization');

$adminEmail = \Yii::$app->params['adminEmail'];

?>

<div class="site-login">

    <?php $flash = Yii::$app->session->getFlash('error') ?>

        <?php
            $form = ActiveForm::begin([
                'fieldConfig' => [
                    'template' => '{input}{label}',
                    'options' => ['class' => 'form-label-group'],
                ],
                'options' => [
                    'class' => 'form-signin'
                ]
            ]);
        ?>

        <div class="text-center mb-4">
            <img class="mb-4" src="/img/cyberFT.svg" alt="CyberFT" width="130">
            <h1 class="h3 mb-3 font-weight-normal"><?=Yii::t('app', 'Login')?></h1>
            <p>
                <?=Yii::t('app', 'If you cannot log in, contact your system administrator')?>
                <?php if ($adminEmail): ?>
                    <a href="<?= Html::encode($adminEmail) ?>"><?= Html::encode($adminEmail) ?></a>
                <?php endif; ?>
            </p>
        </div>

        <div class="text-danger text-center mb-4">
            <?php if ($flash): ?>
                <?=$flash ?>
            <?php endif; ?>
        </div>

        <?php
            echo $form
                ->field($model, 'username')
                ->textInput(['placeholder' => $model->getAttributeLabel('username'), 'required' => true, 'autofocus' => true])
                ->label($model->getAttributeLabel('username'));
            echo $form
                ->field($model, 'password')
                ->passwordInput(['placeholder' => $model->getAttributeLabel('password'), 'required' => true])
                ->label($model->getAttributeLabel('password'));

            echo Html::submitButton(Yii::t('app', 'Sign in'), ['class' => 'btn btn-lg btn-primary btn-block']);
        ?>
        <p class="mt-5 mb-3 text-muted text-center">&copy; <?= date('Y') ?> CyberFT</p>

        <?php
            ActiveForm::end();
        ?>
</div>


