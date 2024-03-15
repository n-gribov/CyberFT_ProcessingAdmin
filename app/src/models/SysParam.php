<?php

namespace app\models;

use app\models\ActiveRecord;
use Yii;

/**
 * This is the model class for table "cyberft.v_sys_params".
 *
 * @property string $par_code
 * @property string $par_value
 * @property string $par_descr
 * @property int $is_visible
 * @property int $is_modifiable
 * @property int $par_type
 * @property string $par_type_name
 * @property string $par_type_descr
 */
class SysParam extends ActiveRecord
{
    const SCENARIO_UPDATE = 'update';
    const PAR_TYPE_INT = 1;
    const PAR_TYPE_STRING = 2;
    const PAR_TYPE_DATE = 3;
    const PAR_TYPE_TIME = 4;
    const PAR_TYPE_BOOL =5;

    public static function tableName()
    {
        return 'cyberft.v_sys_params';
    }

    public static function primaryKey()
    {
        return ['par_code'];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UPDATE] = ['par_value'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            ['par_value', 'validateParValue'],
            [['par_descr', 'par_type_descr'], 'string'],
            [['is_visible', 'is_modifiable', 'par_type', 'par_value'], 'default', 'value' => null],
            [['is_visible', 'is_modifiable', 'par_type'], 'integer'],
            [['par_code', 'par_type_name'], 'string', 'max' => 125],
        ];
    }

    public function attributeLabels()
    {
        return [
            'par_code' => Yii::t('app/sys_param', 'Code'),
            'par_value' => Yii::t('app/sys_param', 'Value'),
            'par_descr' => Yii::t('app/sys_param', 'Description'),
            'is_visible' => Yii::t('app/sys_param', 'Is Visible'),
            'is_modifiable' => Yii::t('app/sys_param', 'Is Modifiable'),
            'par_type' => Yii::t('app/sys_param', 'Par Type'),
            'par_type_name' => Yii::t('app/sys_param', 'Par Type Name'),
            'par_type_descr' => Yii::t('app/sys_param', 'Par Type Descr'),
        ];
    }

    protected function executeInsert()
    {
        return false;
    }

    protected function executeUpdate()
    {
        $query = '
            select
                piIsErrorOut as "hasError",
                pcErrCodeOut as "errorCode",
                pcErrMsgOut as "errorMessage"
            from cyberft.p_param_api_edit(
                :pccode,
                :pcvalue
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':pccode' => $this->par_code,
                    ':pcvalue' => $this->par_value,
                ]
            )
            ->queryOne();

        return $this->processUpdateQueryResult($result);
    }

    protected function executeDelete()
    {
        return false;
    }

    public function validateParValue($attribute)
    {
        if ($this->par_type == self::PAR_TYPE_INT) {
            if (!ctype_digit($this->par_value)) {
                $this->addError($attribute, Yii::t('app/sys_param', 'The field value must be an integer'));
            }
        } elseif ($this->par_type == self::PAR_TYPE_TIME) {
            if (!preg_match('/^([0-2][0-3]|[0-1][0-9]):[0-5][0-9]$/i', $this->par_value)) {
                $this->addError($attribute,
                    Yii::t('app/sys_param', 'The value must match the format hh:mm, for example 14:30'));
            }
        } elseif ($this->par_type == self::PAR_TYPE_DATE) {
            if (!dateValidate($this->par_value)) {
                $this->addError($attribute,
                    Yii::t('app/sys_param', 'The value must match the format dd.mm.yy, for example 03.06.19'));
            }
        } elseif ($this->par_type == self::PAR_TYPE_BOOL) {
            if (!in_array($this->par_value, ['0', '1'])) {
                $this->addError($attribute, Yii::t('app/sys_param', 'The field value must be 0 or 1'));
            }
        }
    }

    private function dateValidate($date)
    {
        if (preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/i', $date, $matches)) {
            return checkdate($matches[2], $matches[1], $matches[3]);
        } else {
            return false;
        }
    }
}
