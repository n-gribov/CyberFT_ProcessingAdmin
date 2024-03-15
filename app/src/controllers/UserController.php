<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\UserSearch;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class UserController extends BaseController
{
    public function behaviors()
    {
        $parent = parent::behaviors();
        $parent['verbs']['actions'] = [
            'delete' => ['POST'],
        ];

        return $parent;
    }

    public function beforeAction($action)
    {
        if (in_array($action->id, ['view', 'create', 'update'])) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $title = Yii::t('app/user', 'User') . ": " . $model->user_name;

        return $this->makeModalData($model, $title, 'view');
    }

    public function actionCreate()
    {
        $model = new User();
        $model->scenario = User::SCENARIO_CREATE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app/user', 'User successfully created'));
            return $this->redirect(Yii::$app->request->referrer);
        }

        $title = Yii::t('app/user', 'New user');

        return $this->makeModalData($model, $title, '_form');
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = User::SCENARIO_UPDATE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app/user', 'The user is successfully changed'));
            return $this->redirect(Yii::$app->request->referrer);
        }

        $title = Yii::t('app/user', 'Update user');

        return $this->makeModalData($model, $title, '_form');
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        Yii::$app->session->setFlash('success', Yii::t('app/user', 'The user status is set to deleted'));

        return $this->redirect(Yii::$app->request->referrer);
    }

    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function makeModalData($model, $title, $contentView)
    {
        $content = $this->renderAjax($contentView, [
            'model' => $model,
        ]);

        return compact('content', 'title');
    }
}
