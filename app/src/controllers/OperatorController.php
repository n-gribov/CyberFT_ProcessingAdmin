<?php

namespace app\controllers;

use app\models\participants\operator\OperatorRole;
use app\models\participants\participant\Participant;
use app\models\participants\processing\Processing;
use app\models\participants\terminal\Terminal;
use Yii;
use app\models\participants\operator\Operator;
use app\models\participants\operator\OperatorSearch;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class OperatorController extends BaseController
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
        $searchModel = new OperatorSearch();
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
        $title = Yii::t('app/operator', 'Operator');

        return $this->makeModalData($model, $title, 'view');
    }

    public function actionCreate($parentId = null, $participantId = null)
    {
        $model = new Operator();
        $model->scenario = Operator::SCENARIO_CREATE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $addKeyUrl = Url::to(['/key', 'KeySearch[owner_id]' => $model->operator_id, '#' => "create({$model->operator_id})"]);
            $successMessage = Yii::t(
                'app/operator',
                'Operator is created, <a href="{addKeyUrl}">add key</a>',
                ['addKeyUrl' => $addKeyUrl]
            );
            Yii::$app->session->setFlash('success', $successMessage);
            return $this->redirect(Yii::$app->request->referrer);
        }

        Yii::$app->gon->push('terminalsByParticipants', $this->getTerminalsByParticipants());

        if (Yii::$app->request->isGet && !empty($parentId)) {
            $terminal = Terminal::findOne($parentId);
            $model->terminal_id = $parentId;
            $model->member_id = $terminal ? $terminal->member_id : null;
        }

        if (Yii::$app->request->isGet && !empty($participantId)) {
            $model->member_id = $participantId;
        }

        $title = Yii::t('app/operator', 'New operator');

        return $this->makeModalData(
            $model,
            $title,
            '_form',
            $this->makeFromData($model)
        );
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = Operator::SCENARIO_UPDATE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app/operator', 'Operator is updated'));
            return $this->redirect(Yii::$app->request->referrer);
        }

        $title = Yii::t('app/operator', 'Update operator');

        return $this->makeModalData(
            $model,
            $title,
            '_form',
            $this->makeFromData($model)
        );
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        Yii::$app->session->setFlash('success', Yii::t('app/operator', 'Operator is deleted'));

        return $this->redirect(Yii::$app->request->referrer);
    }

    private function findModel($id)
    {
        if (($model = Operator::findOne($id)) !== null) {
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

    private function makeFromData(Operator $model)
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

        $roles = OperatorRole::find()->all();

        if ($model->member_id) {
            $terminals = Terminal::find()
                ->where(['member_id' => $model->member_id])
                ->notDeleted()
                ->orderBy('terminal_name')
                ->all();
        } else {
            $terminals = [];
        }


        return compact('processings', 'participants', 'terminals', 'roles');
    }

    private function getTerminalsByParticipants()
    {
        $terminals = Terminal::find()
            ->select(['terminal_id', 'terminal_name', 'member_id'])
            ->notDeleted()
            ->asArray()
            ->all();

        return array_reduce(
            $terminals,
            function (array $carry, array $terminal) {
                $participantId = $terminal['member_id'];
                if (!isset($carry[$participantId])) {
                    $carry[$participantId] = [];
                }
                $carry[$participantId][] = [
                    'id' => $terminal['terminal_id'],
                    'name' => $terminal['terminal_name'],
                ];
                return $carry;
            },
            []
        );
    }

    private function createIndexPageTitle($memberId): string
    {
        $defaultTitle = Yii::t('app/operator', 'Operators');
        if (!$memberId) {
            return $defaultTitle;
        }

        $participant = Participant::findOne($memberId);
        if ($participant === null) {
            return $defaultTitle;
        }

        return Yii::t('app/operator', 'Participant operators "{participantName}" ({participantCode})',
            [
                'participantName' => $participant->member_name,
                'participantCode' => $participant->swift_code
            ]

        );
    }
}
