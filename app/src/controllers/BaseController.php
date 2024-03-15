<?php

namespace app\controllers;

use app\helpers\UserHelper;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\filters\AccessControl;

class BaseController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (!Yii::$app->user->isGuest) {
            $data = [
                'username' => Yii::$app->user->identity->username,
                'password' => Yii::$app->user->identity->password
            ];

            UserHelper::setDBData($data);
        }

        if ($action->id == 'index' || $action->id == ''){
            Yii::$app->gon->push('controllerName', $this->id);
            Yii::$app->gon->push('actionName', $this->action->id);
        }

        Yii::$app->gon->push('language', Yii::$app->language);

        return parent::beforeAction($action);
    }
}