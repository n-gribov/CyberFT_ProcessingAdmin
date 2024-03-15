<?php

namespace app\controllers;

use app\models\dictionaries\country\Country;
use app\models\dictionaries\language\Language;
use app\models\participants\member\BlockMemberForm;
use app\models\participants\member\MemberStatus;
use app\models\participants\participant\Participant;
use app\models\participants\participant\ParticipantSearch;
use app\models\participants\processing\Processing;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
* ParticipantController implements the CRUD actions for Participant model.
*/
class ParticipantController extends BaseController
{
    /**
    * {@inheritdoc}
    */
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

    /**
    * Lists all Participant models.
    * @return mixed
    */
    public function actionIndex()
    {
        $searchModel = new ParticipantSearch(['status' => MemberStatus::STATUS_NOT_DELETED]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
    * Displays a single Participant model.
    * @param integer $id
    * @return mixed
    * @throws NotFoundHttpException if the model cannot be found
    */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $title = Yii::t('app/participant', 'Participant');

        return $this->makeModalData($model, $title, '@app/views/member/view');
    }

    /**
    * Creates a new Participant model.
    * If creation is successful, the browser will be redirected to the 'view' page.
    * @return mixed
    */

    public function actionCreate()
    {
        $model = new Participant();
        $model->scenario = Participant::SCENARIO_CREATE;

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

        $title = Yii::t('app/participant', 'New participant');        
        
        return $this->makeModalData(
            $model,
            $title,
            '@app/views/member/_form',
            $this->makeFromData()
        );
    }

    /**
    * Updates an existing Participant model.
    * If update is successful, the browser will be redirected to the 'view' page.
    * @param integer $id
    * @return mixed
    * @throws NotFoundHttpException if the model cannot be found
    */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = Participant::SCENARIO_UPDATE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app/participant', 'Participant is updated'));
            return $this->redirect(Yii::$app->request->referrer);
        }

        $title = Yii::t('app/participant', 'Update participant');

        return $this->makeModalData(
            $model,
            $title,
            '@app/views/member/_form',
            $this->makeFromData()
        );
    }

    /**
    * Deletes an existing Participant model.
    * If deletion is successful, the browser will be redirected to the 'index' page.
    * @param integer $id
    * @return mixed
    * @throws NotFoundHttpException if the model cannot be found
    * @throws \Exception|\Throwable in case delete failed
    */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', Yii::t('app/participant', 'Participant is deleted'));
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
                Yii::$app->session->setFlash('success', Yii::t('app/participant', 'Participant has been blocked'));
            } else {
                Yii::$app->session->setFlash('error', $formModel->getLastDbErrorMessage());
            }
            return $this->redirect(Yii::$app->request->referrer);
        }

        $title = Yii::t('app/participant', 'Block participant');

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
            Yii::$app->session->setFlash('success', Yii::t('app/participant', 'Participant has been unblocked'));
        } else {
            Yii::$app->session->setFlash('error', 'app/key', $model->getLastDbErrorMessage());
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
    * Finds the Participant model based on its primary key value.
    * If the model is not found, a 404 HTTP exception will be thrown.
    * @param integer $id
    * @return Participant the loaded model
    * @throws NotFoundHttpException if the model cannot be found
    */
    protected function findModel($id)
    {
        if (($model = Participant::findOne($id)) !== null) {
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
        $processings = Processing::find()->notDeleted()->all();
        $countries = Country::find()->all();
        $languages = Language::find()->all();

        return compact('processings', 'countries', 'languages');
    }
}
