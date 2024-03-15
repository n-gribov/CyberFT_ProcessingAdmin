<?php

namespace app\controllers;

use app\models\documents\documentType\DocumentType;
use app\models\participants\tariff\MemberTariff;
use app\models\participants\tariff\PrmTariffs;
use app\models\participants\tariff\PrmTariffsExt;
use app\models\participants\tariff\PrmTariffsSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class TariffRuleController extends BaseController
{
    private $_modelExt;
    private $_backId;
    private $_tariffId;
    private $_dataProvider;
    private $_documentTypeList;
    private $_processAsIntervals;
    
    public function beforeAction($action)
    {
        if (in_array($action->id, ['view', 'create', 'update'])) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        return parent::beforeAction($action);
    }
    
    public function actionIndex()
    {
        return ;
    }
    
    public function actionCreate($id)
    {        
        $model = new PrmTariffs();
        $model->scenario = PrmTariffs::SCENARIO_CREATE;
        
        $this->_modelExt = new PrmTariffsExt();
        $this->_modelExt->scenario = PrmTariffsExt::SCENARIO_CREATE;
        
        $memberTariff = MemberTariff::find()
                        ->where(['tariff_id' => $id, 'amnd_state' => 1])
                        ->one();
        $this->_backId = $memberTariff->memb_trf_id;  
        $this->_tariffId = $id;
        
        if ( $model->load(Yii::$app->request->post()) 
                && $model->save() 
                )  {   
            if (isset(Yii::$app->request->post()['PrmTariffsExt']['intervals_amounts'])){
                $intervals = json_decode( Yii::$app->request->post()['PrmTariffsExt']['intervals_amounts'] );      
                    
                for($i = 0; $i < count($intervals); $i++){
                    $tempModel = new PrmTariffsExt();
                    $tempModel->trf_prm_id = $model->trf_prm_id;
                    $tempModel->docs_cnt_from = $intervals[$i][0];
                    $tempModel->docs_cnt_to = ($i !== count($intervals) - 1) ? $intervals[$i+1][0] - 1 : '2147483647';
                    $tempModel->price_sent_doc = $intervals[$i][1];
                    $tempModel->price_sent_mb = 0;
                    $tempModel->save();
                }                    
                
            } else {
                $this->_modelExt->load(Yii::$app->request->post());
                $this->_modelExt->trf_prm_id = $model->trf_prm_id;
                $this->_modelExt->docs_cnt_from = 1;
                $this->_modelExt->docs_cnt_to = 2147483647;
                
                
                $this->_modelExt->save(); 
            }
            
            Yii::$app->session->setFlash('success', Yii::t('app/tariff', 'Tariff parameter is created'));
            
            return Yii::$app->runAction('tariff/update', ['id' => $memberTariff->memb_trf_id]);
                       
        }        
        
        $title = Yii::t('app/tariff', 'Add tariff parameter');
        
        $documentTypeModel = new DocumentType();
        $this->_documentTypeList =  $documentTypeModel->getDocumentTypeList($id);  
        
        return $this->makeModalData(
            $model,
            $title,
            '_form_create',
            $this->makeFromData()
        );
        
    }
    
    public function actionUpdate($id, $extId, $tariffId)
    {        
        //extId используется для случая без интервалов
        $model = $this->findModel($id);
        $model->scenario = PrmTariffs::SCENARIO_UPDATE;      
        
        $this->_modelExt = $this->findModelExt($extId); 
        $this->_modelExt->scenario = PrmTariffsExt::SCENARIO_UPDATE;
        
        $memberTariff = MemberTariff::find()
                        ->where(['tariff_id' => $tariffId, 'amnd_state' => 1, 'status' => 1])
                        ->one();        
        $this->_backId = $memberTariff->memb_trf_id;          
        
        
        if ( $this->_modelExt->load(Yii::$app->request->post()) ) 
        {
            $post = Yii::$app->request->post();
            /*
             * Определяем, имеем ли мы дело с интервалами (много записей)
             * или с единственной записью
             */
            $prm_tariffs_ext = PrmTariffsExt::find()
                    ->select('trf_prm_ext_id')
                    ->where(['trf_prm_id' => $id, 'amnd_state' => 1, 'status' => 1])
                    ->asArray()
                    ->all();
            
            if (count($prm_tariffs_ext) > 1 || array_key_exists('intervals_amounts',$post['PrmTariffsExt'])) {
                
                foreach($prm_tariffs_ext as $ext) {
                    $tempModel = PrmTariffsExt::findOne($ext['trf_prm_ext_id']);
                    $tempModel->delete();
                }
                
                //После удаления данных по параметру надо удалить сам параметр и создать новый, иначе новые данные не запишутся
                $tempModelValues = [
                    $model->trf_id,
                    $model->msg_type_id,
                    $model->is_register
                ];
                                
                $model->delete();
                
                $model = new PrmTariffs();
                $model->trf_id = $tempModelValues[0];
                $model->msg_type_id = $tempModelValues[1];
                $model->is_register = $tempModelValues[2];
                $model->save();
                $model->scenario = PrmTariffs::SCENARIO_UPDATE;
                
                //Здесь будет добавление
                
                if ( array_key_exists('intervals_amounts',$post['PrmTariffsExt']) )
                {
                    $intervals = json_decode( $post['PrmTariffsExt']['intervals_amounts'] );  
                    
                    for($i = 0; $i < count($intervals); $i++){
                        $tempModel = new PrmTariffsExt();
                        $tempModel->trf_prm_id = $model->trf_prm_id;
                        $tempModel->docs_cnt_from = $intervals[$i][0];
                        $tempModel->docs_cnt_to = ($i !== count($intervals) - 1) ? $intervals[$i+1][0] - 1 : '2147483647';
                        $tempModel->price_sent_doc = $intervals[$i][1];
                        $tempModel->price_sent_mb = 0;
                        $tempModel->save();
                    }              
                } else {
                    $this->_modelExt = new PrmTariffsExt();
                    $this->_modelExt->scenario = PrmTariffsExt::SCENARIO_CREATE;
                    
                    $this->_modelExt->load(Yii::$app->request->post());
                    
                    $this->_modelExt->trf_prm_id = $model->trf_prm_id;        
                    $this->_modelExt->docs_cnt_from = 1;        
                    $this->_modelExt->docs_cnt_to = 2147483647;        
                    
                    //В правилах на документы может быть либо стоимость за документ, либо стоимость за мегабайт, не обе сразу
                    if ( array_key_exists('price_sent_doc', $post['PrmTariffsExt']) ) {
                        $this->_modelExt->price_sent_mb = 0;                    
                    } else if ( array_key_exists('price_sent_mb', $post['PrmTariffsExt']) ) {
                        $this->_modelExt->price_sent_doc = 0;                    
                    }

                    $this->_modelExt->save();
                
                }
                Yii::$app->session->setFlash('success', Yii::t('app/tariff', 'Tariff parameter is updated'));
                
                //При сохранении возвращаемся на страницу назад                
                return Yii::$app->runAction('tariff/update', ['id' => $memberTariff->memb_trf_id]);
                
            } else {
                                    
                $this->_modelExt->trf_prm_id = $model->trf_prm_id;        

                //В правилах на документы может быть либо стоимость за документ, либо стоимость за мегабайт, не обе сразу
                if ( array_key_exists('price_sent_doc', $post['PrmTariffsExt']) ) {
                    $this->_modelExt->price_sent_mb = 0;                    
                } else if ( array_key_exists('price_sent_mb', $post['PrmTariffsExt']) ) {
                    $this->_modelExt->price_sent_doc = 0;                    
                }

                $this->_modelExt->save();
                
                Yii::$app->session->setFlash('success', Yii::t('app/tariff', 'Tariff parameter is updated'));
                
                //При сохранении возвращаемся на страницу назад
                return Yii::$app->runAction('tariff/update', ['id' => $memberTariff->memb_trf_id]);
            }
        }
       
        //Определяем опцию по умолчанию для select (только для реестров)
        $this->_processAsIntervals = false;
        if ($model->is_register == 1) {
            $models = $this->actionGenerateTablePrm($model->msg_type_id, $model->trf_id);
            if (count($models) > 1) $this->_processAsIntervals = true;
            if ($this->_modelExt->docs_cnt_from > 1) $this->_processAsIntervals = true;
        }
        
        $title = Yii::t('app/tariff', 'Update tariff parameter');
        
        return $this->makeModalData(
            $model,
            $title,
            '_form_update',
            $this->makeFromData()
        );
        
    }        
    
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $memberTariff = MemberTariff::find()
                            ->where(['tariff_id' => $model->trf_id])
                            ->one();        
        $model->delete();

        return Yii::$app->runAction('tariff/update', ['id' => $memberTariff->memb_trf_id]);
    }
    
    public function actionGenerateTablePrm($msg_type_id, $trf_id)
    {                
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $searchModel = new PrmTariffsSearch();
        $search = $searchModel->searchAmounts(['msg_type_id' => $msg_type_id, 'trf_id' => $trf_id, 'is_register' => 1]);        
        $dataProvider = $search['dataProvider'];
        
        \Yii::info('=== GENERATE');
                
        return $dataProvider->getModels();
    }
    
    public function actionDocumentTypeList($q = null, $tariff_id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
    
        $model = new DocumentType();
        return $model->getDocumentTypeList($q, $tariff_id);
    }        
    
    public function actionTarifficationOptions()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $model = new DocumentType();
        return $model->tarifficationOptions;
    }
    
    private function findModel($id)
    {        
        if (($model = PrmTariffs::findOne($id)) !== null) {
            return $model;
        } 

       throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    private function findModelExt($extId)
    {        
        if (($model = PrmTariffsExt::findOne($extId)) !== null) {
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
        
        $data['modelExt'] = $this->_modelExt;
        $data['backId'] = $this->_backId;
        $data['tariffId'] = $this->_tariffId;
        $data['dataProvider'] = $this->_dataProvider;
        $data['documentTypeList'] = $this->_documentTypeList;
        $data['processAsIntervals'] = $this->_processAsIntervals;
        
        return $data;
    }

}
