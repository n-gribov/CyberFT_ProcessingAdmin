<?php

namespace app\models\documents\documentType;

use app\models\ActiveRecord;
use app\models\participants\tariff\PrmTariffs;
use Yii;
use yii\web\Response;

/**
 * This is the model class for table "cyberft.v_message_types".
 *
 * @property int $type_id
 * @property string $message_code
 * @property string $message_name
 * @property string $info
 * @property int $status
 * @property int $need_permission
 * @property int $broadcast
 * @property string $i_date
 * @property string $i_user
 * @property string $u_date
 * @property string $u_user
 * @property string $d_date
 * @property string $d_user
 * @property int $group_id
 * @property string $group_name
 * @property int $system_id
 * @property string $system_name
 * @property int $system_message
 */
class DocumentType extends ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    private $_tariffication;

    public static function tableName()
    {
        return 'cyberft.v_message_types';
    }

    public static function primaryKey()
    {
        return ['type_id'];
    }

    public static function find()
    {
        return new DocumentTypeQuery(get_called_class());
    }

    public function getTariff()
    {
        return $this->hasOne(DocumentTypeTariff::className(), ['msg_type_id' => 'type_id'])
                    ->andWhere('status = 1')
                    ->andWhere('e_date > current_date')
                    ->andWhere('b_date <= current_date');
    }

    public function getTariffication()
    {
        if ($this->scenario != self::SCENARIO_CREATE) {
            return $this->tariff == TRUE ? 1 : 0;
        } else {
            return $this->_tariffication;
        }
    }

    public function setTariffication($value)
    {
        $this->_tariffication = $value;
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $assignableAttributes = [
            'type_id',
            'message_code',
            'message_name',
            'status',
            'need_permission',
            'broadcast',
            'group_id',
            'system_id',
            'system_message',
            'tariffication',
            'is_register',
        ];
        $scenarios[self::SCENARIO_CREATE] = $assignableAttributes;
        $scenarios[self::SCENARIO_UPDATE] = $assignableAttributes;
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['type_id', 'status', 'group_id', 'system_id'], 'default', 'value' => null],
            [['need_permission', 'broadcast', 'system_message'], 'default', 'value' => 0],
            [['type_id', 'status', 'need_permission', 'broadcast', 'group_id', 'system_id', 'system_message'], 'integer'],
            [['info'], 'string'],
            [['info'], 'default', 'value' => ''],
            [['i_date', 'u_date', 'd_date'], 'safe'],
            [['message_code', 'i_user', 'u_user', 'd_user'], 'string', 'max' => 80],
            [['message_name', 'group_name', 'system_name'], 'string', 'max' => 255],
            [['is_register', 'tariffication'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'message_code' => Yii::t('app/document-type', 'Code'),
            'message_name' => Yii::t('app/document-type', 'Title'),
            'status' => Yii::t('app/document-type', 'Status'),
            'i_date' => Yii::t('app/document-type', 'Time of creation'),
            'i_user' => Yii::t('app/document-type', 'Created by'),
            'u_date' => Yii::t('app/document-type', 'Time change'),
            'u_user' => Yii::t('app/document-type', 'Changed by'),
            //'d_date' => Yii::t('app/document-type', 'Removal time'),
            //'d_user' => Yii::t('app/document-type', 'Deleted by'),
            'group_id' => Yii::t('app/document-type', 'Group'),
            'group_name' => Yii::t('app/document-type', 'Group'),
            'system_name' => Yii::t('app/document-type', 'System'),
            'tariffication' => Yii::t('app/document-type', 'Tariffication'),
            'is_register' => Yii::t('app/document-type', 'Register'),
        ];
    }

    protected function executeInsert()
    {
        $query = '
            select
                piIsErrorOut as "hasError",
                pcErrCodeOut as "errorCode",
                pcErrMsgOut as "errorMessage",
                piMessageTypeOut as "id"
            from cyberft.p_message_api_create_message_type(
                :pcMsgCode,
                :pcMsgName,
                :pcInfo,
                :piNeedPerm,
                :piBrodcast,
                :piIsRegister,
                :piGroup,
                :piSystemMessage
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':pcMsgCode' => $this->message_code,
                    ':pcMsgName' => $this->message_name,
                    ':pcInfo' => $this->info,
                    ':piNeedPerm' => $this->need_permission,
                    ':piBrodcast' => $this->broadcast,
                    ':piIsRegister' => $this->is_register,
                    ':piGroup' => $this->group_id,
                    ':piSystemMessage' => $this->system_message,
                ]
            )
            ->queryOne();

        if ($result['hasError'] == FALSE) {
            $tariffResult = $this->addTariff($result['id']);
            if ($tariffResult['hasError'] == TRUE) {
                return $this->processInsertQueryResult($tariffResult);
            }
        }

        return $this->processInsertQueryResult($result);
    }

    protected function executeUpdate()
    {
        $query = '
            select
                piIsErrorOut as "hasError",
                pcErrCodeOut as "errorCode",
                pcErrMsgOut as "errorMessage"
            from cyberft.p_message_api_update_message_type(
                :piMsgType,
                :pcMsgCode,
                :pcMsgName,
                :pcInfo,
                :piNeedPerm,
                :piBrodcast,
                :piIsRegister,
                :piGroup,
                :piSystemMessage
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':piMsgType' => $this->type_id,
                    ':pcMsgCode' => $this->message_code,
                    ':pcMsgName' => $this->message_name,
                    ':pcInfo' => $this->info,
                    ':piNeedPerm' => $this->need_permission,
                    ':piBrodcast' => $this->broadcast,
                    ':piIsRegister' => $this->is_register,
                    ':piGroup' => $this->group_id,
                    ':piSystemMessage' => $this->system_message,
                ]
            )
            ->queryOne();

        if ($result['hasError'] == FALSE) {
            $tariffResult = $this->addTariff($this->type_id);
            if ($tariffResult['hasError'] == TRUE) {
                return $this->processInsertQueryResult($tariffResult);
            }
        }

        return $this->processUpdateQueryResult($result);
    }

    protected function executeDelete()
    {
        $query = '
            select
                piiserrorout as "hasError",
                pcerrcodeout as "errorCode",
                pcerrmsgout as "errorMessage"
            from cyberft.p_message_api_delete_message_type(:piMsgType)
        ';

        $result = Yii::$app->db
            ->createCommand($query, [':piMsgType' => $this->type_id])
            ->queryOne();

        return $this->processDeleteQueryResult($result);
    }

    private function addTariff($id)
    {
        $tariff = new DocumentTypeTariff();
        $tariff->scenario = DocumentTypeTariff::SCENARIO_CREATE;
        $tariff->msg_type_id = $id;
        $tariff->status = $this->_tariffication;
        $saveResult = $tariff->save();
        return [
            'hasError' => $saveResult === false ? 1 : 0,
            'errorCode' => $tariff->getLastDbErrorCode(),
            'errorMessage' => $tariff->getLastDbErrorMessage(),
            'id' => $saveResult ? $tariff->primaryKey : null,
        ];
    }
    
    public function getDocumentTypeList($q = null, $tariff_id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;    
        
            $documentTypeWithTarifficationList = $this->find()
                                            ->where($this->_tariffication == 1)
                                            ->all();            
                
            if( $tariff_id ) {
                $message_ids = PrmTariffs::find()->select('msg_type_id, is_register')
                              ->where(['trf_id' => $tariff_id, 'amnd_state' => 1, 'status' => 1])                        
                              ->asArray()
                              ->all();
            } else {
                $message_ids = [];
            }

            $out['results'] = [];

            /* Найти в таблице cyberft.v_tariffs_prm существующие записи message_id
             * Если там есть -1 (все документы/реестры), то проверяем также на is_register 
             * Соответствующие типы документов/реестров не включаем в меню, чтобы не повторялись
             */


            if (!in_array(['msg_type_id' => '-1', 'is_register' => '0'], $message_ids)){
                $out['results'][] = [
                        'id' => -1,
                        'text' => Yii::t('app/document-type', 'All documents'),
                        'is_register' => 0
                ];
            }

            if (!in_array(['msg_type_id' => '-1', 'is_register' => '1'], $message_ids)){
                $out['results'][] = [ 
                        'id' => -2,
                        'text' => Yii::t('app/document-type', 'All registers'),
                        'is_register' => 1
                ];
            }

            foreach ($documentTypeWithTarifficationList as $item) {

                if(!in_array(['msg_type_id' => $item['type_id'], 'is_register' => $item['is_register']], $message_ids)
                        && ($item['status'] == 1)){

                    $out['results'][] = [ 
                        'id' => $item['type_id'],
                        'text' => $item['message_name'].' ('.$item['message_code'].')',
                        'is_register' => $item['is_register']
                    ];
                } 

            }
            
            if ($q != null) {
                $temp_array = [];
                foreach($out['results'] as $item) {
                    if (strpos(mb_strtolower($item['text']), mb_strtolower($q)) !== FALSE){
                        array_push($temp_array, $item);
                    }                        
                }
                $out['results'] = $temp_array;
            }
            
            return $out;
        

        
    }
}
