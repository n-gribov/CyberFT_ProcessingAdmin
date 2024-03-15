<?php

namespace app\models\participants\tariff;

use app\models\ActiveRecord;
use app\models\documents\documentType\DocumentType;
use Yii;

/**
 * This is the model class for table "cyberft.v_tariffs_prm".
 *
 * @property int $trf_prm_id
 * @property int $trf_id
 * @property int $msg_type_id
 * @property int $is_register
 * @property int $amnd_state
 * @property string $amnd_date
 * @property int $prnt_id
 * @property string $author
 * @property DocumentType $documentType[]
 * @property PrmTariffsExt $prmTariffsExt[]
 */
class PrmTariffs extends ActiveRecord
{
    protected $_select = [];
    
    public $documentTypeList;
    public $registryAmountOption;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public static function tableName()
    {
        return 'cyberft.v_tariffs_prm';
    }

    public static function primaryKey()
    {
        return ['trf_prm_id'];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $assignableAttributes = [
            'trf_prm_id', 'trf_id', 'msg_type_id', 'is_register'
        ];
        $scenarios[self::SCENARIO_CREATE] = $assignableAttributes;
        $scenarios[self::SCENARIO_UPDATE] = $assignableAttributes;
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['trf_prm_id', 'trf_id', 'msg_type_id', 'is_register', 'amnd_state', 'prnt_id'], 'default', 'value' => null],
            [['trf_prm_id', 'trf_id', 'msg_type_id', 'is_register', 'amnd_state', 'prnt_id'], 'integer'],
            [['amnd_date'], 'safe'],
            [['author'], 'string', 'max' => 300],
        ];
    }

    public function attributeLabels()
    {
        return [
            'trf_prm_id' => Yii::t('app', 'Trf Prm ID'),
            'trf_id' => Yii::t('app', 'Trf ID'),
            'msg_type_id' => Yii::t('app', 'Msg Type ID'),
            'is_register' => Yii::t('app', 'Is Register'),
            'amnd_state' => Yii::t('app', 'Amnd State'),
            'amnd_date' => Yii::t('app', 'Amnd Date'),
            'prnt_id' => Yii::t('app', 'Prnt ID'),
            'author' => Yii::t('app', 'Author'),
        ];
    }

    protected function executeInsert()
    {
        $query = '
            select
                piIsErrorOut as "hasError",
                pcErrCodeOut as "errorCode",
                pcErrMsgOut as "errorMessage",
                io_trf_prm_id as "id"
            from cyberft.p_mng_api_tariff_prm_set ( 
                :io_trf_prm_id,
                :i_trf_id,
                :i_msg_type_id,
                :i_is_register,
                :i_status
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':io_trf_prm_id' => null,
                    ':i_trf_id'     => $this->trf_id,
                    ':i_msg_type_id' => $this->msg_type_id,
                    ':i_is_register' => $this->is_register,                    
                    ':i_status' => 1
                ]
            )
            ->queryOne();

        return $this->processInsertQueryResult($result);
    }

    protected function executeUpdate()
    {
        $query = '
            select
                piIsErrorOut as "hasError",
                pcErrCodeOut as "errorCode",
                pcErrMsgOut as "errorMessage",
                io_trf_prm_id as "id"
            from cyberft.p_mng_api_tariff_prm_set ( 
                :i_io_trf_prm_id,
                :i_i_trf_id,
                :i_msg_type_id,
                :i_is_register,
                :i_status
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':io_trf_prm_id' => $this->trf_prm_id,
                    ':i_trf_id'     => $this->trf_id,
                    ':i_msg_type_id' => $this->msg_type_id,
                    ':i_is_register' => $this->is_register,                    
                    ':i_status' => 1
                ]
            )
            ->queryOne();

        return $this->processUpdateQueryResult($result);
    }

    protected function executeDelete()
    {
        $query = '
            select
                piIsErrorOut as "hasError",
                pcErrCodeOut as "errorCode",
                pcErrMsgOut as "errorMessage",
                io_trf_prm_id as "id"
            from cyberft.p_mng_api_tariff_prm_set ( 
                :io_trf_prm_id,
                :i_trf_id,
                :i_msg_type_id,
                :i_is_register,
                :i_status
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':io_trf_prm_id' => $this->trf_prm_id,
                    ':i_trf_id'     => null,
                    ':i_msg_type_id' => null,
                    ':i_is_register' => null,
                    ':i_status' => 0
                ]
            )
            ->queryOne();

        return $this->processDeleteQueryResult($result);
    }
    
    public function getPrmTariffsExt()
    {
        return $this->hasMany(PrmTariffsExt::className(), ['trf_prm_id' => 'trf_prm_id']);
    }
    
    public function getDocumentType()
    {
        return $this->hasMany(DocumentType::className(), ['type_id' => 'msg_type_id']);
    }
}
