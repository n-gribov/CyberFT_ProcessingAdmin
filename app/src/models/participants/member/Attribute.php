<?php

namespace app\models\participants\member;

use app\models\ActiveRecord;
use Yii;
use yii\base\NotSupportedException;

/**
 * This is the model class for table "cyberft.v_attributes".
 *
 * @property int attr_id
 * @property string attr_name
 * @property string descr
 * @property string i_date
 * @property string i_user
 * @property string u_date
 * @property string u_user
 */
class Attribute extends ActiveRecord
{
    public static function tableName()
    {
        return 'cyberft.v_attributes';
    }

    public static function primaryKey()
    {
        return ['attr_id'];
    }

    protected function executeInsert()
    {
        $query = '
            select
                piIsErrorOut as "hasError",
                pcErrCodeOut as "errorCode",
                pcErrMsgOut as "errorMessage",
                piAttrOut as "id"
            from cyberft.p_member_api_add_attribute(
                :pcName,
                :pcDescr
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':pcName'  => $this->attr_name,
                    ':pcDescr' => $this->descr,
                ]
            )
            ->queryOne();

        return $this->processInsertQueryResult($result);
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
