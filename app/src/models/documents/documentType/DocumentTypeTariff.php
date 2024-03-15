<?php

namespace app\models\documents\documentType;

use app\models\ActiveRecord;
use Yii;

/**
 * This is the model class for table "cyberft.v_message_type_tariff".
 *
 * @property int $msg_type_id
 * @property string $b_date
 * @property string $e_date
 * @property int $status
 * @property int $u_utype
 * @property string $u_date
 * @property string $u_user
 */
class DocumentTypeTariff extends ActiveRecord
{
    const SCENARIO_CREATE = 'create';

    public static function tableName()
    {
        return 'cyberft.v_message_type_tariff';
    }

    public static function primaryKey()
    {
        return ['msg_type_id'];
    }

    public function getDocumentType()
    {
        return $this->hasOne(DocumentType::className(), ['type_id' => 'msg_type_id']);
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['msg_type_id', 'status'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['msg_type_id', 'status', 'u_utype'], 'default', 'value' => null],
            [['msg_type_id', 'status', 'u_utype'], 'integer'],
            [['b_date', 'e_date', 'u_date'], 'safe'],
            [['u_user'], 'string', 'max' => 80],
        ];
    }

    public function attributeLabels()
    {
        return [];
    }

    protected function executeInsert()
    {
        $query = '
            select
                piiserrorout as "hasError",
                pcerrcodeout as "errorCode",
                pcerrmsgout as "errorMessage"
            from cyberft.p_message_api_set_tariff(
                :piMsgType,
                :piStatus
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':piMsgType' => $this->msg_type_id,
                    ':piStatus' => $this->status,
                ]
            )
            ->queryOne();

        if ($result['hasError'] == FALSE) {
            $result['id'] = $this->msg_type_id;
        }

        return $this->processInsertQueryResult($result);
    }

    protected function executeUpdate()
    {
        return FALSE;
    }

    protected function executeDelete()
    {
        return FALSE;
    }
}
