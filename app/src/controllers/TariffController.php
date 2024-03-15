<?php

namespace app\controllers;

use app\models\participants\tariff\MemberTariff;
use app\models\participants\tariff\PrmTariffsSearch;
use Yii;
use yii\data\SqlDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class TariffController extends BaseController
{
    private $_participantId;
    private $_dataProvider;
    private $_arrayCount;
    
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
        return;
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $title = Yii::t('app/tariff', 'Member tariff');

        return $this->makeModalData($model, $title, 'view');
    }

    public function actionCreate($participantId)
    {
        $model = new MemberTariff();
        $model->scenario = MemberTariff::SCENARIO_CREATE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {            
            // Сразу переходим к модалке с обновлением после успешного создания тарифа с абонентской платой
            Yii::$app->session->setFlash('success', Yii::t('app/tariff', 'Member tariff is created'));
            
            // Загружаем модель заново, чтобы получить правильное значение memb_trf_id
            $model = MemberTariff::find()
                                    ->where(['member_id' => $model->member_id, 'amnd_state' => 1, 'status' => 1])
                                    ->one();            
            $model->scenario = MemberTariff::SCENARIO_UPDATE;
            
            $title = Yii::t('app/tariff', 'Update member tariff');
        
            $this->_dataProvider = null;
        
            $this->_participantId = $participantId;
            
            $this->_arrayCount = [];            

            return $this->makeModalData(
                $model,
                $title,
                '_form',
                $this->makeFromData()
            );
        }

        $title = Yii::t('app/tariff', 'New member tariff');
        
        $this->_participantId = $participantId;

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
        $model->scenario = MemberTariff::SCENARIO_UPDATE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {   
            // Все равно продолжаем заново добавлять элементы в модалку при сохранении формы
            Yii::$app->session->setFlash('success', Yii::t('app/tariff', 'Member tariff is updated'));
        }

        $title = Yii::t('app/tariff', 'Update member tariff');
        
        $this->_participantId = $model->member_id;
        $searchModel = new PrmTariffsSearch();
        $search = $searchModel->search(['trf_id' => $model->tariff_id]);
        
        $this->_dataProvider = $search['dataProvider'];
        
        // Нужно для того, чтобы вычислять rowspan'ы в таблице
        $this->_arrayCount = [];
        foreach($search['arrayCount'] as $item){
            $trf_prm_id = $item['trf_prm_id'];
            $count = $item['count'];            
            $this->_arrayCount[$trf_prm_id] = $count;
        }

        return $this->makeModalData(
            $model,
            $title,
            '_form',
            $this->makeFromData()
        );
    }
    
    public function actionDelete($id)
    {
        //  (CYB-4527) Пока что в задаче не предусмотрено удаление тарифа участника
    }
    

    private function findModel($id)
    {        
        if (($model = MemberTariff::findOne($id)) !== null) {
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
        $data = [];
        
        $data['participantId'] = $this->_participantId;
        $data['dataProvider'] = $this->_dataProvider;
        $data['arrayCount'] = $this->_arrayCount;
        
        return $data;
    }
}
