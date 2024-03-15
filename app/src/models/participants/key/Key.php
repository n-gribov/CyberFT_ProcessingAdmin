<?php

namespace app\models\participants\key;

use app\models\ActiveRecord;
use Yii;
use yii\db\Query;
use app\models\participants\operator\Operator;

/**
 * This is the model class for table "cyberft.v_keys".
 *
 * @property int $key_id
 * @property int $owner_type
 * @property string $owner_type_name
 * @property int $owner_id
 * @property string $owner_code
 * @property string $swift_code
 * @property string $owner_name
 * @property int $key_type
 * @property string $key_type_name
 * @property int $crypto_type
 * @property int $cert_center
 * @property string $key_code
 * @property int $status
 * @property string $status_name
 * @property string $block_info
 * @property string $start_date
 * @property string $end_date
 * @property string $i_date
 * @property string $i_user
 * @property string $d_date
 * @property string $d_user
 * @property string $b_date
 * @property string $b_user
 * @property string $key_body
 * @property string $u_date
 * @property string $u_user
 * @property string $change_info
 */
class Key extends ActiveRecord
{
    const KEY_TYPE_PUBLIC = 2;
    const KEY_OWNER_TYPE_OPERATOR = 2;
    const STATUS_ACTIVE = 1;
    const STATUS_BLOCKED = 2;
    const STATUS_AWAITING_ACTIVATION = 3;
    const ROLE_SIGNATORY = 21;
    const ROLE_CONTROLLER = 22;

    public static function tableName()
    {
        return 'cyberft.v_keys';
    }

    public static function primaryKey()
    {
        return ['key_id'];
    }

    public function getRole()
    {
        return $this->hasOne(Operator::className(), ['operator_id' => 'owner_id']);
    }

    public function getOwnerRole()
    {
        $role = $this->role;
        if ($role) {
            return $role->role_name;
        }
    }

    public static function find(): KeyQuery
    {
        return (new KeyQuery(get_called_class()))
            ->andWhere(['owner_type' => static::KEY_OWNER_TYPE_OPERATOR])
            ->andWhere(['key_type' => static::KEY_TYPE_PUBLIC]);
    }

    public function rules()
    {
        return [
            [['key_id', 'owner_type', 'owner_id', 'key_type', 'crypto_type', 'cert_center', 'status'], 'default', 'value' => null],
            [['key_id', 'owner_type', 'owner_id', 'key_type', 'crypto_type', 'cert_center', 'status'], 'integer'],
            [['key_body', 'change_info'], 'string'],
            [['key_code'], 'string', 'max' => 80],
            [['block_info'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'key_id' => Yii::t('app/model', 'ID'),
            'owner_type' => Yii::t('app/key', 'Owner type'),
            'owner_type_name' => Yii::t('app/key', 'Owner type'),
            'owner_id' => Yii::t('app/model', 'Owner'),
            'owner_code' => Yii::t('app/key', 'Owner code'),
            'swift_code' => Yii::t('app/key', 'Swift code'),
            'owner_name' => Yii::t('app/model', 'Owner'),
            'key_type' => Yii::t('app/model', 'Type'),
            'key_type_name' => Yii::t('app/key', 'Type'),
            'crypto_type' => Yii::t('app/key', 'Crypto yype'),
            'cert_center' => Yii::t('app/key', 'Cert center'),
            'key_code' => Yii::t('app/model', 'Code'),
            'status' => Yii::t('app/model', 'Status'),
            'status_name' => Yii::t('app/model', 'Status'),
            'block_info' => Yii::t('app/model', 'Blocking reason'),
            'start_date' => Yii::t('app/key', 'Start date'),
            'end_date' => Yii::t('app/key', 'End date'),
            'i_date' => Yii::t('app/model', 'Created at'),
            'i_user' => Yii::t('app/model', 'Created by'),
            'd_date' => Yii::t('app/model', 'Deleted at'),
            'd_user' => Yii::t('app/model', 'Deleted by'),
            'b_date' => Yii::t('app/model', 'Blocked at'),
            'b_user' => Yii::t('app/model', 'Blocked by'),
            'u_date' => Yii::t('app/model', 'Updated at'),
            'u_user' => Yii::t('app/model', 'Updated by'),
            'key_body' => Yii::t('app/key', 'Certificate'),
            'change_info' => Yii::t('app/key', 'Change reason'),
        ];
    }

    protected function executeInsert()
    {
        $query = '
            select
                piIsErrorOut as "hasError",
                pcErrCodeOut as "errorCode",
                pcErrMsgOut as "errorMessage",
                piKeyOut as "id"
            from cyberft.p_member_api_add_key(
                :piOwnerType,
                :piOwner,
                :piKeyType,
                :piCryptoType,
                :piCertCenter,
                :pcKeyCode,
                :pdatStartDate,
                :pdatEndDate,
                :pbKeyBody
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':piOwnerType'   => $this->owner_type,
                    ':piOwner'       => $this->owner_id,
                    ':piKeyType'     => $this->key_type,
                    ':piCryptoType'  => 0,
                    ':piCertCenter'  => 0,
                    ':pcKeyCode'     => $this->key_code,
                    ':pdatStartDate' => $this->start_date,
                    ':pdatEndDate'   => $this->end_date,
                    ':pbKeyBody'     => $this->key_body,
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
                pcErrMsgOut as "errorMessage"
            from cyberft.p_member_api_edit_key(
                :piKey,
                :pdatStartDate,
                :pdatEndDate,
                :pcChandeInfo
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':piKey'         => $this->key_id,
                    ':pdatStartDate' => $this->start_date,
                    ':pdatEndDate'   => $this->end_date,
                    ':pcChandeInfo'  => $this->change_info,
                ]
            )
            ->queryOne();

        return $this->processUpdateQueryResult($result);
    }

    protected function executeDelete()
    {
        $query = '
            select
                piiserrorout as "hasError",
                pcerrcodeout as "errorCode",
                pcerrmsgout as "errorMessage"
            from cyberft.p_member_api_delete_key(:piKey)
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [':piKey' => $this->key_id]
            )
            ->queryOne();

        return $this->processDeleteQueryResult($result);
    }

    public function executeUnblock()
    {
        return $this->executeLock(0);
    }

    public function executeBlock()
    {
        return $this->executeLock(1);
    }

    private function executeLock($lock)
    {
        $query = '
            select
                piiserrorout as "hasError",
                pcerrcodeout as "errorCode",
                pcerrmsgout as "errorMessage"
            from cyberft.p_member_api_block_key(
                :piKey,
                :piBlock,
                :pcBlockInfo
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    'piKey' => $this->key_id,
                    'piBlock' => $lock,
                    'pcBlockInfo' => $this->block_info,
                ]
            )
            ->queryOne();

        return $this->processUpdateQueryResult($result);
    }

    public function executeActivate()
    {
        $query = '
            select
                piiserrorout as "hasError",
                pcerrcodeout as "errorCode",
                pcerrmsgout as "errorMessage"
            from cyberft.p_member_api_activate_key(:piKeyId)
        ';

        $result = Yii::$app->db
            ->createCommand($query,['piKeyId' => (int) $this->key_id])
            ->queryOne();

        return $this->processUpdateQueryResult($result);
    }
}
