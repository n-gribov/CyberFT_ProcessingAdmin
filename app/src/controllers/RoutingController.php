<?php

namespace app\controllers;

use Yii;
use app\models\Routing;
use app\models\RoutingSearch;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class RoutingController extends BaseController
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
        if (in_array($action->id, ['create'])) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $searchModel = new RoutingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Routing();
        $model->scenario = Routing::SCENARIO_CREATE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app/routing', 'Route is created'));
            return $this->redirect(Yii::$app->request->referrer);
        }

        $title = Yii::t('app/routing', 'New route');

        return $this->makeModalData(
            $model,
            $title,
            '_form',
            $this->makeFromData()
        );
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        Yii::$app->session->setFlash('success', Yii::t('app/routing', 'Route is deleted'));

        return $this->redirect(Yii::$app->request->referrer);
    }

    private function findModel($id)
    {
        if (($model = Routing::findOne($id)) !== null) {
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
