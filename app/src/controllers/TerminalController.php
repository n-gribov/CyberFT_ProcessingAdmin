<?php

namespace app\controllers;

use app\models\participants\participant\Participant;
use app\models\participants\processing\Processing;
use app\models\participants\terminal\BlockTerminalForm;
use app\models\participants\terminal\Terminal;
use app\models\participants\terminal\TerminalSearch;
use app\models\participants\terminal\TerminalWorkMode;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class TerminalController extends BaseController
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
        $searchModel = new TerminalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pageTitle' => $this->createIndexPageTitle($searchModel->member_id),
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $title = Yii::t('app/terminal', 'Terminal');

        return $this->makeModalData($model, $title, 'view');
    }

    public function actionCreate($parentId = null)
    {
        $model = new Terminal();
        $model->scenario = Terminal::SCENARIO_CREATE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $successMessage = Yii::t(
                'app/terminal',
                'Terminal is created, <a href="/operator#create({terminalId})">add operator</a>',
                ['terminalId' => $model->terminal_id]
            );
            Yii::$app->session->setFlash('success', $successMessage);
            return $this->redirect(Yii::$app->request->referrer);
        }

        if (Yii::$app->request->isGet && !empty($parentId)) {
            $model->member_id = $parentId;
        }

        $title = Yii::t('app/terminal', 'New terminal');

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
        $model->scenario = Terminal::SCENARIO_UPDATE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app/terminal', 'Terminal is updated'));
            return $this->redirect(Yii::$app->request->referrer);
        }

        $title = Yii::t('app/terminal', 'Update terminal');

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

        Yii::$app->session->setFlash('success', Yii::t('app/terminal', 'Terminal is deleted'));

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionBlock($id)
    {
        $model = $this->findModel($id);
        $formModel = BlockTerminalForm::create($model);
        if (Yii::$app->request->isPost && $formModel->load(Yii::$app->request->post()) && $formModel->validate()) {
            if ($formModel->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app/terminal', 'Terminal has been blocked'));
            } else {
                Yii::$app->session->setFlash('error', $formModel->getLastDbErrorMessage());
            }
            return $this->redirect(Yii::$app->request->referrer);
        }

        $title = Yii::t('app/terminal', 'Block terminal');

        return $this->makeModalData(
            $formModel,
            $title,
            '@app/views/terminal/_blockForm',
            $this->makeFromData()
        );
    }

    public function actionUnblock($id)
    {
        $model = $this->findModel($id);

        if ($model->setBlockStatus(false, null)) {
            Yii::$app->session->setFlash('success', Yii::t('app/terminal', 'Terminal has been unblocked'));
        } else {
            Yii::$app->session->setFlash('error', 'app/key', $model->getLastDbErrorMessage());
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    protected function findModel($id)
    {
        if (($model = Terminal::findOne($id)) !== null) {
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
        $processings = Processing::find()
            ->notDeleted()
            ->orderBy('proc_name')
            ->all();
        $participants = Participant::find()
            ->notDeleted()
            ->belongsToPrimaryProcessing()
            ->orderBy('member_name')
            ->all();
        $workModes = TerminalWorkMode::all();

        return compact('processings', 'participants', 'workModes');
    }

    private function createIndexPageTitle($memberId)
    {
        $defaultTitle = Yii::t('app/terminal', 'Terminals');
        if (!$memberId) {
            return $defaultTitle;
        }

        $participant = Participant::findOne(['member_id' => $memberId]);
        if ($participant === null) {
            return $defaultTitle;
        }

        return Yii::t('app/terminal', 'Participant terminals "{participantName}" ({participantCode})',
            [
             'participantName' => $participant->member_name,
             'participantCode' => $participant->swift_code
            ]
        );
    }
}
