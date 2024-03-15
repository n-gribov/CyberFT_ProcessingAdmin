<?php

namespace app\controllers;

use Yii;
use app\models\SysParam;
use app\models\SysParamSearch;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SysParamController extends BaseController
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
        if (in_array($action->id, ['update'])) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $searchModel = new SysParamSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = SysParam::SCENARIO_UPDATE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app/sys_param', 'System parameters have been updated'));
            return $this->redirect(Yii::$app->request->referrer);
        }

        $title = Yii::t('app/sys_param', 'Edit system parameters');

        return $this->makeModalData(
            $model,
            $title,
            '_form',
            $this->makeFromData()
        );
    }

    private function findModel($id)
    {
        if (($model = SysParam::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private function makeModalData($model, $title, $contentView, $viewData = [])
    {
        $content = $this->renderAjax(
            $contentView,
            array_merge($viewData, ['model' => $model])
        );

        return compact('content', 'title');
    }

    private function makeFromData()
    {
        return [];
    }
}
