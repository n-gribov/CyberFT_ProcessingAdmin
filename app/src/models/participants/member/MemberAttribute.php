<?php

namespace app\models\participants\member;

use app\models\ActiveRecord;
use Yii;
use yii\base\NotSupportedException;

/**
 * This is the model class for table "cyberft.v_member_attributes".
 *
 * @property int mattr_id
 * @property int attr_id
 * @property string attr_name
 * @property int member_id
 * @property string member_swift_code
 * @property string member_name
 * @property int member_type
 * @property string attr_value
 * @property string i_date
 * @property string i_user
 */
class MemberAttribute extends ActiveRecord
{
    public static function tableName()
    {
        return 'cyberft.v_member_attributes';
    }

    public static function primaryKey()
    {
        return ['mattr_id'];
    }

    protected function executeInsert()
    {
        $query = '
            select
                piIsErrorOut as "hasError",
                pcErrCodeOut as "errorCode",
                pcErrMsgOut as "errorMessage",
                piMAttrout as "id"
            from cyberft.p_member_api_add_member_attr(
                :piMember,
                :piAttr,
                :pcValue
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':piMember' => $this->member_id,
                    ':piAttr'   => $this->attr_id,
                    ':pcValue'  => $this->attr_value,
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
        $query = '
            select
                piiserrorout as "hasError",
                pcerrcodeout as "errorCode",
                pcerrmsgout as "errorMessage"
            from cyberft.p_member_api_del_member_attr(:piMAttr)
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [':piMAttr' => $this->mattr_id]
            )
            ->queryOne();

        return $this->processDeleteQueryResult($result);
    }
}
