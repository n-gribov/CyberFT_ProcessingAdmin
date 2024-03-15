<?php

namespace app\controllers;

use app\models\dictionaries\country\Country;
use app\models\dictionaries\language\Language;
use app\models\participants\member\BlockMemberForm;
use app\models\participants\member\MemberStatus;
use app\models\participants\processing\Processing;
use app\models\participants\processing\ProcessingSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ProcessingController extends BaseController
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
        if (in_array($action->id, ['view', 'create', 'update', 'block', 'unblock'])) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $searchModel = new ProcessingSearch(['status' => MemberStatus::STATUS_NOT_DELETED]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $title = Yii::t('app/participant', 'Processing');

        return $this->makeModalData($model, $title, '@app/views/member/view');
    }

    public function actionCreate()
    {
        $model = new Processing();
        $model->scenario = Processing::SCENARIO_CREATE;

        if (Yii::$app->request->isGet) {
            $localProcessing = Processing::find()
                ->notDeleted()
                ->andWhere(['is_primary' => 1])
                ->one();
            $model->parent_id = $localProcessing ? $localProcessing->member_id : null;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/terminal', '#' => "create($model->member_id)"]);
        }

        $title = Yii::t('app/participant', 'New processing');
        
        return $this->makeModalData(
            $model,
            $title,
            '@app/views/member/_form',
            $this->makeFromData()
        );
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = Processing::SCENARIO_UPDATE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app/participant', 'Processing is updated'));
            return $this->redirect(Yii::$app->request->referrer);
        }

        $title = Yii::t('app/participant', 'Update processing');

        return $this->makeModalData(
            $model,
            $title,
            '@app/views/member/_form',
            $this->makeFromData()
        );
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->is_primary) {
            Yii::$app->session->setFlash('error', Yii::t('app/participant', 'Primary processing cannot be deleted'));
            return $this->redirect(Yii::$app->request->referrer);
        }

        if ($model->delete()) {
            Yii::$app->session->setFlash('success', Yii::t('app/participant', 'Processing is deleted'));
        } else {
            Yii::$app->session->setFlash('error', $model->getLastDbErrorMessage());
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionBlock($id)
    {
        $model = $this->findModel($id);
        $formModel = BlockMemberForm::create($model);
        if (Yii::$app->request->isPost && $formModel->load(Yii::$app->request->post()) && $formModel->validate()) {
            if ($formModel->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app/participant', 'Processing has been blocked'));
            } else {
                Yii::$app->session->setFlash('error', $formModel->getLastDbErrorMessage());
            }
            return $this->redirect(Yii::$app->request->referrer);
        }

        $title = Yii::t('app/participant', 'Block processing');

        return $this->makeModalData(
            $formModel,
            $title,
            '@app/views/member/_blockForm',
            $this->makeFromData()
        );
    }

    public function actionUnblock($id)
    {
        $model = $this->findModel($id);

        if ($model->setBlockStatus(false, null)) {
            Yii::$app->session->setFlash('success', Yii::t('app/participant', 'Processing has been unblocked'));
        } else {
            Yii::$app->session->setFlash('error', 'app/key', $model->getLastDbErrorMessage());
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    protected function findModel($id)
    {
        if (($model = Processing::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function makeModalData($model, $title, $contentView, $viewData = [])
    {
        $content = $this->renderAjax(
            $contentView,
            array_merge($viewData, ['model' => $model])
        );

        return compact('content', 'title');
    }

    protected function makeFromData()
    {
        $processings = [];
        $countries = Country::find()->all();
        $languages = Language::find()->all();

        return compact('processings', 'countries', 'languages');
    }
}
