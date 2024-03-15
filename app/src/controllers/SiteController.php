<?php

namespace app\controllers;

use app\helpers\UserHelper;
use Yii;
use app\models\LoginForm;

class SiteController extends BaseController
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->redirect(['/document']);
    }

    public function actionLogin()
    {
        $this->layout = 'login';

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if (Yii::$app->request->isPost) {
            try {
                $model->load(Yii::$app->request->post());

                if ($model->login()) {
                    return $this->redirect('/document');
                } else{
                    throw new \Exception('Invalid username/password');
                }
            } catch(\Exception $e) {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Authorization error, you entered an incorrect login or password'));
                $model->password = '';
            }
        }

        return $this->render('login', compact('model'));
    }

    public function actionLogout()
    {
        UserHelper::deleteCachedIdentityData();

        Yii::$app->user->logout();

        return $this->goHome();
    }
}
