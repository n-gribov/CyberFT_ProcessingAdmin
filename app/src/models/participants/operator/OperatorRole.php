<?php

namespace app\models\participants\operator;

use app\models\ActiveRecord;
use Yii;
use yii\base\NotSupportedException;

/**
 * This is the model class for table "cyberft.v_operator_roles".
 *
 * @property int $role_id
 * @property string $role_code
 * @property string $role_name
 */
class OperatorRole extends ActiveRecord
{
    const ROLE_ADMINISTRATOR = 11;

    public static function tableName()
    {
        return 'cyberft.v_operator_roles';
    }

    public static function primaryKey()
    {
        return ['role_id'];
    }

    public function rules()
    {
        return [
            [['role_id'], 'integer'],
            [['role_code', 'role_name'], 'string', 'max' => 80],
        ];
    }

    public function attributeLabels()
    {
        return [
            'role_id' => Yii::t('app/model', 'ID'),
            'role_code' => Yii::t('app/model', 'Code'),
            'role_name' => Yii::t('app/model', 'Name'),
        ];
    }

    protected function executeInsert()
    {
        throw new NotSupportedException();
    }

    protected function executeUpdate()
    {
        throw new NotSupportedException();
    }

    protected function executeDelete()
    {
        throw new NotSupportedException();
    }
}
