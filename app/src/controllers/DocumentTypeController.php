<?php

namespace app\controllers;

use Yii;
use app\models\documents\documentType\DocumentType;
use app\models\documents\documentType\DocumentTypeSearch;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class DocumentTypeController extends BaseController
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
        $searchModel = new DocumentTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $title = Yii::t('app/document-type', 'Document type');

        return $this->makeModalData($model, $title, 'view');
    }

    public function actionCreate()
    {
        $model = new DocumentType();
        $model->scenario = DocumentType::SCENARIO_CREATE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app/document-type', 'Document type is created'));
            return $this->redirect(Yii::$app->request->referrer);
        }

        $title = Yii::t('app/document-type', 'New document type');

        return $this->makeModalData(
            $model,
            $title,
            '_form',
            $this->makeFromData()
        );
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = DocumentType::SCENARIO_UPDATE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app/document-type', 'Document type is updated'));
            return $this->redirect(Yii::$app->request->referrer);
        }

        $title = Yii::t('app/document-type', 'Update document type');

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

        Yii::$app->session->setFlash('success', Yii::t('app/document-type', 'Document type is deleted'));

        return $this->redirect(Yii::$app->request->referrer);
    }

    private function findModel($id)
    {
        if (($model = DocumentType::findOne($id)) !== null) {
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
