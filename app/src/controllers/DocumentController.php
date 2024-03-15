<?php

namespace app\controllers;

use app\models\documents\document\Document;
use app\models\documents\document\DocumentQuery;
use app\models\documents\document\DocumentSearch;
use app\models\documents\document\DocumentsExcelReport;
use app\models\documents\document\DocumentStatus;
use app\models\documents\documentType\DocumentType;
use app\services\documents\ResendDocumentsService;
use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class DocumentController extends BaseController
{
    public function beforeAction($action)
    {
        if (in_array($action->id, ['view', 'resend', 'suspend-delivery', 'save-message-code-filter-selection'])) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $searchModel = $this->createSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $statuses = DocumentStatus::all();
        asort($statuses);
        $types = DocumentType::find()
            ->notDeleted()
            ->orderBy('message_code')
            ->all();

        Yii::$app->gon->push('count', $dataProvider->totalCount);
        $exportUrl = Url::to(array_merge(['/document/export'], Yii::$app->request->queryParams));

        return $this->render(
            'index',
            compact('searchModel', 'dataProvider', 'statuses', 'types', 'exportUrl')
        );
    }

    public function actionExport()
    {
        $searchModel = $this->createSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setPagination(false);

        $maxDocumentsCount = 30_000;
        if ($dataProvider->query->count() > $maxDocumentsCount) {
            Yii::$app->session->setFlash(
                'error',
                Yii::t(
                    'app/document',
                    'Too many documents selected. Export file cannot include more than {maxCount} documents. Please, specify search conditions and try again.',
                    ['maxCount' => $maxDocumentsCount]
                )
            );
            return $this->redirect(array_merge(['/document'], Yii::$app->request->queryParams));
        }

        $report = new DocumentsExcelReport($dataProvider->query);
        return $this->response->sendContentAsFile($report->generate(), $report->getFileName());
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->showDocument($model);
    }

    public function actionResend($id)
    {
        $model = $this->findModel($id);
        $isResent = $model->resend();

        if ($isResent) {
            Yii::$app->session->setFlash('success', Yii::t('app/document', 'Document is queued for resending'));
        } else {
            $errorMessage = $model->getLastDbErrorMessage() ?: Yii::t('app/document', 'Failed to queue document for resending');
            Yii::$app->session->setFlash('error', $errorMessage);
        }

        return $this->showDocument($model);
    }

    public function actionBatchResendByIds()
    {
        $ids = Yii::$app->request->post('ids');
        $query = Document::find()->where(['message_id' => $ids]);
        $this->batchResend($query);
        return $this->redirect(Yii::$app->request->referrer ?: '/document');
    }

    public function actionBatchResendBySearchParams()
    {
        $params = Yii::$app->request->post();
        $searchModel = $this->createSearchModel();
        if (!$searchModel->load($params) || !$searchModel->validate()) {
            throw new \Exception('Failed to populate search model');
        }

        $dataProvider = $searchModel->search($params);
        $dataProvider->setPagination(false);

        $this->batchResend($dataProvider->query);
        return $this->redirect(Yii::$app->request->referrer ?: '/document');
    }

    private function batchResend(DocumentQuery $query): void
    {
        try {
            $service = new ResendDocumentsService($query);
            $resendResult = $service->resend();
            Yii::$app->session->setFlash('info', $this->renderPartial('_resend-result', ['result' => $resendResult]));
        } catch (\Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
            $errorMessage = $exception instanceof \DomainException
                ? $exception->getMessage()
                : Yii::t('app/document', 'Failed to queue documents for resending');
            Yii::$app->session->setFlash('error', $errorMessage);
        }
    }

    public function actionSuspendDelivery($id)
    {
        $model = $this->findModel($id);
        $isUpdated = $model->updateStatus(DocumentStatus::SUSPENDED);

        if ($isUpdated) {
            Yii::$app->session->setFlash('success', Yii::t('app/document', 'Document delivery is suspended'));
        } else {
            $errorMessage = $model->getLastDbErrorMessage() ?: Yii::t('app/document', 'Failed to cancel document delivery');
            Yii::$app->session->setFlash('error', $errorMessage);
        }

        return $this->showDocument($model);
    }

    public function actionSaveMessageCodeFilterSelection()
    {
        $values = json_decode(Yii::$app->request->rawBody, true, JSON_THROW_ON_ERROR);
        $selectionId = md5(Yii::$app->request->rawBody);
        Yii::$app->session->set($this->createMessageCodeSelectionKey($selectionId), $values);
        return ['id' => $selectionId];
    }

    private function createMessageCodeSelectionKey(string $selectionId): string
    {
        return "documentsSearchMessageCodeSelection_$selectionId";
    }

    private function showDocument(Document $model): array
    {
        $title = Yii::t('app/document', 'Document') . ': ' . $model->message_name;
        return $this->makeModalData($model, $title, '_view');
    }

    private function findModel($id): Document
    {
        if (($model = Document::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private function createSearchModel(): DocumentSearch
    {
        return new DocumentSearch([
            'messageCodeSelectionResolver' => function (string $selectionId) {
                $values = Yii::$app->session->get($this->createMessageCodeSelectionKey($selectionId));
                if (!is_array($values)) {
                    throw new \Exception("Failed to load message codes for selection id $selectionId");
                }
                return $values;
            },
        ]);
    }

    // TODO: remove duplicate
    protected function makeModalData($model, $title, $contentView)
    {
        $content = $this->renderAjax($contentView, [
            'model' => $model,
        ]);

        return compact('content', 'title');
    }
}
