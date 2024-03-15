<?php

namespace app\models\participants\tariff;

use app\models\ActiveRecord;
use Yii;

/**
 * This is the model class for table "cyberft.v_tariffs_prm_ext".
 *
 * @property int $trf_prm_ext_id
 * @property int $trf_prm_id
 * @property int $docs_cnt_from
 * @property int $docs_cnt_to
 * @property string $price_sent_doc
 * @property string $price_sent_mb
 * @property int $amnd_state
 * @property string $amnd_date
 * @property int $prnt_id
 * @property string $author
 */
class PrmTariffsExt extends ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    
    public $intervals_amounts;
    public $doc_min;
    public $amount;
    public $documentTarifficationOption;

    public static function tableName()
    {
        return 'cyberft.v_tariffs_prm_ext';
    }

    public static function primaryKey()
    {
        return ['trf_prm_ext_id'];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $assignableAttributes = [
            'trf_prm_ext_id',
            'trf_prm_id',
            'docs_cnt_from',
            'docs_cnt_to',
            'price_sent_doc',
            'price_sent_mb'
        ];
        $scenarios[self::SCENARIO_CREATE] = $assignableAttributes;
        $scenarios[self::SCENARIO_UPDATE] = $assignableAttributes;
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['trf_prm_ext_id', 'trf_prm_id', 'docs_cnt_from', 'docs_cnt_to', 'amnd_state', 'prnt_id'], 'default', 'value' => null],
            [['trf_prm_ext_id', 'trf_prm_id', 'docs_cnt_from', 'docs_cnt_to', 'amnd_state', 'prnt_id'], 'integer'],
            [['docs_cnt_from', 'docs_cnt_to'], 'integer', 'min' => 0],
            [['price_sent_doc', 'price_sent_mb'], 'number', 'min' => 0],
            [['amnd_date'], 'safe'],
            [['author'], 'string', 'max' => 300],
        ];
    }

    public function attributeLabels()
    {
        return [
            'trf_prm_ext_id' => Yii::t('app', 'Trf Prm Ext ID'),
            'trf_prm_id' => Yii::t('app', 'Trf Prm ID'),
            'docs_cnt_from' => Yii::t('app', 'Docs Cnt From'),
            'docs_cnt_to' => Yii::t('app', 'Docs Cnt To'),
            'price_sent_doc' => Yii::t('app/tariff', 'Price per document'),
            'price_sent_mb' => Yii::t('app/tariff', 'Price per MB'),
            'amnd_state' => Yii::t('app', 'Amnd State'),
            'amnd_date' => Yii::t('app', 'Amnd Date'),
            'prnt_id' => Yii::t('app', 'Prnt ID'),
            'author' => Yii::t('app', 'Author'),
        ];
    }
    
    public function beforeValidate()
    {
        /**
         * Для того, чтобы можно было вводить запятые в интервалах для реестра
         */
        $this->price_sent_doc = str_replace(',', '.', $this->price_sent_doc);
        $this->price_sent_mb = str_replace(',', '.', $this->price_sent_mb);

        return parent::beforeValidate();
    }

    protected function executeInsert()
    {
        $query = '
            select
                piIsErrorOut as "hasError",
                pcErrCodeOut as "errorCode",
                pcErrMsgOut as "errorMessage",
                io_trf_prm_ext_id as "id"
            from cyberft.p_mng_api_tariff_prm_ext_set ( 
                :io_trf_prm_ext_id,
                :i_trf_prm_id,
                :i_docs_cnt_from,
                :i_docs_cnt_to,
                :i_price_sent_doc,
                :i_price_sent_mb
            )
        ';
        

        $price_sent_doc = $this->price_sent_doc ? $this->price_sent_doc : '0';
        $price_sent_mb = $this->price_sent_mb ? $this->price_sent_mb : '0';
        
        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':io_trf_prm_ext_id' => null,
                    ':i_trf_prm_id' => $this->trf_prm_id,
                    ':i_docs_cnt_from' => $this->docs_cnt_from,
                    ':i_docs_cnt_to' => $this->docs_cnt_to,
                    ':i_price_sent_doc' => $price_sent_doc,
                    ':i_price_sent_mb' => $price_sent_mb
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
                io_trf_prm_ext_id as "id"
            from cyberft.p_mng_api_tariff_prm_ext_set ( 
                :io_trf_prm_ext_id,
                :i_trf_prm_id,
                :i_docs_cnt_from,
                :i_docs_cnt_to,
                :i_price_sent_doc,
                :i_price_sent_mb
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':io_trf_prm_ext_id' => $this->trf_prm_ext_id,
                    ':i_trf_prm_id'     => $this->trf_prm_id,
                    ':i_docs_cnt_from'  => $this->docs_cnt_from,
                    ':i_docs_cnt_to'    => $this->docs_cnt_to,
                    ':i_price_sent_doc' => $this->price_sent_doc,
                    ':i_price_sent_mb'  => $this->price_sent_mb
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
                io_trf_prm_ext_id as "id"
            from cyberft.p_mng_api_tariff_prm_ext_set ( 
                :io_trf_prm_ext_id,
                :i_trf_prm_id,
                :i_docs_cnt_from,
                :i_docs_cnt_to,
                :i_price_sent_doc,
                :i_price_sent_mb,
                :i_status
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':io_trf_prm_ext_id' => $this->trf_prm_ext_id,
                    ':i_trf_prm_id'     => null,
                    ':i_docs_cnt_from'  => null,
                    ':i_docs_cnt_to'    => null,
                    ':i_price_sent_doc' => null,
                    ':i_price_sent_mb'  => null,
                    ':i_status'         => 0
                ]
            )
            ->queryOne();

        return $this->processDeleteQueryResult($result);
    }
}
