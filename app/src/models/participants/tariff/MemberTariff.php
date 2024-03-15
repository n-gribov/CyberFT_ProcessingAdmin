<?php

namespace app\models\participants\tariff;

use app\models\ActiveRecord;
use Yii;

/**
 * This is the model class for table "cyberft.v_member_tariff".
 *
 * @property int $memb_trf_id
 * @property int $member_id
 * @property int $tariff_id
 * @property string $fix_pay
 * @property string $i_date
 * @property string $i_user
 * @property int $amd_stade
 * @property int $prnt_id
 */
class MemberTariff extends ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public static function tableName()
    {
        return 'cyberft.v_member_tariff';
    }

    public static function primaryKey()
    {
        return ['memb_trf_id'];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $assignableAttributes = [
            'member_id', 'tariff_id', 'fix_pay'
        ];
        $scenarios[self::SCENARIO_CREATE] = $assignableAttributes;
        $scenarios[self::SCENARIO_UPDATE] = $assignableAttributes;
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['memb_trf_id', 'member_id', 'tariff_id', 'amd_stade', 'prnt_id'], 'default', 'value' => null],
            [['memb_trf_id', 'member_id', 'tariff_id', 'amd_stade', 'prnt_id'], 'integer'],
            [['fix_pay'], 'number', 'min' => 0],
            [['i_date'], 'safe'],
            [['i_user'], 'string', 'max' => 300],
        ];
    }

    public function attributeLabels()
    {
        return [
            'memb_trf_id' => Yii::t('app/tariff', 'Member Tariff ID'),
            'member_id' => Yii::t('app/tariff', 'Member ID'),
            'tariff_id' => Yii::t('app/tariff', 'Tariff ID'),
            'fix_pay' => Yii::t('app/tariff', 'Fixed Pay'),
            'i_date' => Yii::t('app/model', 'Created at'),
            'i_user' => Yii::t('app/model', 'Created by'),
            'amd_stade' => Yii::t('app/tariff', 'Amd Stade'),
            'prnt_id' => Yii::t('app/tariff', 'Print ID'),
        ];
    }

    protected function executeInsert()
    {
        $query = '
            select
                piIsErrorOut as "hasError",
                pcErrCodeOut as "errorCode",
                pcErrMsgOut as "errorMessage",
                io_tariff_id as "id"
            from cyberft.p_mng_api_tariff_for_member_set ( 
                :io_tariff_id,
                :i_member_id,
                :i_fix_pay,
                :i_status
            )
        ';
        
        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':io_tariff_id' => null,
                    ':i_member_id' => $this->member_id,
                    ':i_fix_pay'   => $this->fix_pay,
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
                io_tariff_id as "id"
            from cyberft.p_mng_api_tariff_for_member_set ( 
                :io_tariff_id,
                :i_member_id,
                :i_fix_pay,
                :i_status
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':io_tariff_id' => $this->tariff_id,
                    ':i_member_id' => $this->member_id,
                    ':i_fix_pay'   => $this->fix_pay,
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
                io_tariff_id as "id"
            from cyberft.p_mng_api_tariff_for_member_set ( 
                :io_tariff_id,
                :i_member_id,
                :i_fix_pay,
                :i_status
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':io_tariff_id' => $this->tariff_id,
                    ':i_member_id' => $this->member_id,
                    ':i_fix_pay'   => null,
                    ':i_status' => 0
                ]
            )
            ->queryOne();
        
        return $this->processDeleteQueryResult($result);
    }
}
