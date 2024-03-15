<?php

namespace app\controllers;

use app\models\participants\key\forms\CreateKeyForm;
use app\models\participants\key\forms\UpdateKeyForm;
use app\models\participants\key\forms\UploadKeyForm;
use app\models\participants\key\forms\BlockKeyForm;
use app\models\participants\key\Key;
use app\models\participants\key\KeySearch;
use app\models\participants\operator\Operator;
use app\models\participants\operator\OperatorSearch;
use app\models\participants\participant\Participant;
use app\models\participants\processing\Processing;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class KeyController extends BaseController
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
        if (in_array($action->id, ['view', 'create', 'upload', 'update', 'block', 'unblock'])) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $searchModel = new KeySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pageTitle' => $this->createIndexPageTitle($searchModel->owner_id),
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $title = Yii::t('app/key', 'Key');

        return $this->makeModalData($model, $title, 'view');
    }

    public function actionUpload($parentId = null)
    {
        $uploadForm = new UploadKeyForm();
        if (Yii::$app->request->isGet && !empty($parentId)) {
            $uploadForm->ownerId = $parentId;
        }

        if ($uploadForm->load(Yii::$app->request->post()) && $uploadForm->validate()) {
            $createForm = CreateKeyForm::create($uploadForm->ownerId, $uploadForm->getKeyBody());
            return $this->makeCreateModalData($createForm);
        }

        return $this->makeModalData(
            $uploadForm,
            Yii::t('app/key', 'New key'),
            '_uploadForm',
            $this->makeFromData()
        );
    }

    public function actionCreate($parentId = null)
    {
        if (Yii::$app->request->isGet) {
            return $this->actionUpload($parentId);
        }

        $model = new CreateKeyForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app/key', 'Key is created'));
            return $this->redirect(Yii::$app->request->referrer);
        }

        if (Yii::$app->request->isGet && !empty($parentId)) {
            $model->ownerId = $parentId;
        }

        $title = Yii::t('app/key', 'New key');

        return $this->makeCreateModalData($model);
    }

    private function makeCreateModalData(CreateKeyForm $form)
    {
        $operators = $this->getOperators();

        Yii::$app->gon->push('swiftCodesByOperatorId', $this->crateOperatorIdToSwiftCodeMap($operators));

        return $this->makeModalData(
            $form,
            Yii::t('app/key', 'New key'),
            '_createForm',
            compact('operators')
        );
    }

    private function crateOperatorIdToSwiftCodeMap(array $operators): array
    {
        return array_reduce(
            $operators,
            function (array $carry, Operator $operator) {
                $carry[$operator->operator_id] = $operator->full_swift_code;
                return $carry;
            },
            []
        );
    }

    private function findModel($id): Key
    {
        if (($model = Key::findOne($id)) !== null) {
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
        return ['operators' => $this->getOperators()];
    }

    private function getOperators()
    {
        $processingsIdQuery = Processing::find()
            ->notDeleted()
            ->select('member_id');
        $participantsIdQuery = Participant::find()
            ->notDeleted()
            ->belongsToPrimaryProcessing()
            ->select('member_id');

        return Operator::find()
            ->notDeleted()
            ->andWhere([
                'or',
                ['in', 'member_id', $processingsIdQuery],
                ['in', 'member_id', $participantsIdQuery],
            ])
            ->orderBy('operator_name')
            ->all();
    }

    public function actionUpdate($id)
    {
        $keyModel = $this->findModel($id);
        $formModel = UpdateKeyForm::create($keyModel);

        if ($formModel->load(Yii::$app->request->post()) && $formModel->save($keyModel)) {
            Yii::$app->session->setFlash('success', Yii::t('app/key', 'Key is updated'));
            return $this->redirect(Yii::$app->request->referrer);
        }

        $title = Yii::t('app/key', 'Update key');

        return $this->makeModalData(
            $formModel,
            $title,
            '_updateForm',
            $this->makeFromData()
        );
    }

    public function actionDelete($id)
    {
        $model = $this->findModel((int) $id);

        if ($model->delete() != false) {
            Yii::$app->session->setFlash('success', Yii::t('app/key', 'Key is deleted'));
        } else {
            Yii::$app->session->setFlash('danger', Yii::t('app/key', $model->getLastDbErrorMessage()));
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionActivate($id)
    {
        $model = $this->findModel((int) $id);

        if ($model->executeActivate() != false) {
            Yii::$app->session->setFlash('success', Yii::t('app/key', 'Key has been activated'));
        } else {
            Yii::$app->session->setFlash('danger', Yii::t('app/key', $model->getLastDbErrorMessage()));
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionBlock($id)
    {
        $keyModel = $this->findModel((int) $id);
        $formModel = BlockKeyForm::create($keyModel);

        if ($formModel->load(Yii::$app->request->post()) && $formModel->save($keyModel)) {

            if ($keyModel->executeBlock() != false) {
                Yii::$app->session->setFlash('success', Yii::t('app/key', 'Key has been blocked'));
            } else {
                Yii::$app->session->setFlash('danger', Yii::t('app/key', $keyModel->getLastDbErrorMessage()));
            }

            return $this->redirect(Yii::$app->request->referrer);
        }

        $title = Yii::t('app/key', 'Block key');

        return $this->makeModalData(
            $formModel,
            $title,
            '_blockForm',
            $this->makeFromData()
        );
    }

    public function actionUnblock($id)
    {
        $model = $this->findModel((int) $id);

        if ($model->executeUnblock() != false) {
            Yii::$app->session->setFlash('success', Yii::t('app/key', 'Key has been unblocked'));
        } else {
            Yii::$app->session->setFlash('danger', 'app/key', $model->getLastDbErrorMessage());
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionDownloadKeyFile($id)
    {
        $key = $this->findModel($id);
        Yii::$app->response->sendContentAsFile($key->key_body, "{$key->key_code}.cer");
    }

    private function createIndexPageTitle($operatorId): string
    {
        $defaultTitle = Yii::t('app/key', 'Keys');
        if (!$operatorId) {
            return $defaultTitle;
        }

        $operator = Operator::findOne($operatorId);
        if ($operator === null) {
            return $defaultTitle;
        }

        return Yii::t('app/key', 'Keys of operator {operatorName}', ['operatorName' => $operator->operator_name]);
    }
}
