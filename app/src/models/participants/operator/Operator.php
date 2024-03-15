<?php

namespace app\models\participants\operator;

use app\models\ActiveRecord;
use Yii;
use app\models\participants\key\Key;

/**
 * This is the model class for table "cyberft.v_operators".
 *
 * @property int $operator_id
 * @property int $member_id
 * @property string $member_code
 * @property string $swift_code
 * @property string $member_name
 * @property int $member_type
 * @property string $operator_name
 * @property int $status
 * @property int $blocked
 * @property string $block_info
 * @property int $role_id
 * @property string $role_name
 * @property int $full_privs
 * @property int $terminal_id
 * @property string $terminal_code
 * @property string $full_swift_code
 * @property string $terminal_name
 * @property string $operator_position
 * @property string $phone
 * @property string $email
 * @property string $i_date
 * @property string $i_user
 * @property string $u_date
 * @property string $u_user
 * @property string $status_name
 * @property string $external_code
 */
class Operator extends ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public static function tableName()
    {
        return 'cyberft.v_operators';
    }

    public static function primaryKey()
    {
        return ['operator_id'];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $assignableAttributes = [
            'role_id', 'full_privs', 'external_code', 'operator_name', 'operator_position', 'phone', 'email',
        ];
        $scenarios[self::SCENARIO_CREATE] = array_merge($assignableAttributes, ['member_id', 'terminal_id']);
        $scenarios[self::SCENARIO_UPDATE] = $assignableAttributes;
        return $scenarios;
    }

    public function getKey()
    {
        return $this->hasMany(Key::className(), ['owner_id' => 'operator_id']);
    }

    public function rules()
    {
        return [
            [
                [
                    'operator_id', 'member_id', 'role_id', 'full_privs', 'terminal_id',
                    'operator_position', 'phone', 'email', 'external_code',
                ],
                'default',
                'value' => null
            ],
            [['operator_id', 'member_id', 'role_id', 'full_privs', 'terminal_id'], 'integer'],
            [['external_code'], 'string', 'max' => 80],
            [['operator_name', 'operator_position', 'phone', 'email'], 'string', 'max' => 255],
            [
                [
                    'role_id', 'full_privs', 'external_code', 'operator_name', 'operator_position', 'phone', 'email',
                    'member_id', 'terminal_id'
                ],
                'safe'
            ],
            [['member_id', 'role_id', 'operator_name'], 'required'],
            [
                'terminal_id',
                'required',
                'when' => function (Operator $model) {
                    return !empty($model->role_id) && $model->role_id != OperatorRole::ROLE_ADMINISTRATOR;
                },
                'whenClient' => "function (attribute, value) {
                    var role = $('#operator-role_id').val();
                    return role && role != '" . OperatorRole::ROLE_ADMINISTRATOR . "';
                }"
            ],
            ['email', 'email'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'operator_id' => Yii::t('app/model', 'ID'),
            'member_id' => Yii::t('app/operator', 'Participant'),
            'member_code' => Yii::t('app/operator', 'Participant code'),
            'swift_code' => Yii::t('app/operator', 'Participant Swift code'),
            'member_name' => Yii::t('app/operator', 'Participant'),
            'member_type' => Yii::t('app/operator', 'Participant type'),
            'operator_name' => Yii::t('app/operator', 'Name'),
            'status' => Yii::t('app/model', 'Status'),
            'status_name' => Yii::t('app/model', 'Status'),
            'blocked' => Yii::t('app/model', 'Blocked'),
            'block_info' => Yii::t('app/model', 'Blocking reason'),
            'role_id' => Yii::t('app/operator', 'Role'),
            'role_name' => Yii::t('app/operator', 'Role'),
            'full_privs' => Yii::t('app/operator', 'Access to documents of any type'),
            'terminal_id' => Yii::t('app/terminal', 'Terminal'),
            'terminal_code' => Yii::t('app/operator', 'Terminal code'),
            'full_swift_code' => Yii::t('app/operator', 'Terminal code'),
            'terminal_name' => Yii::t('app/terminal', 'Terminal'),
            'operator_position' => Yii::t('app/operator', 'Position'),
            'phone' => Yii::t('app/operator', 'Phone'),
            'email' => Yii::t('app/operator', 'Email'),
            'i_date' => Yii::t('app/model', 'Created at'),
            'i_user' => Yii::t('app/model', 'Created by'),
            'u_date' => Yii::t('app/model', 'Updated at'),
            'u_user' => Yii::t('app/model', 'Updated by'),
            'external_code' => Yii::t('app/operator', 'External code'),
        ];
    }

    public function getStatus_name()
    {
        switch ($this->status) {
            case 1:
                return Yii::t('app/operator', 'active');
            case 0:
                return Yii::t('app/operator', 'deleted');
        };
        return null;
    }

    public static function find()
    {
        return new OperatorQuery(get_called_class());
    }

    protected function executeInsert()
    {
        $result = $this->role_id == OperatorRole::ROLE_ADMINISTRATOR
            ? $this->insertAdministrator()
            : $this->insertOperator();

        return $this->processInsertQueryResult($result);
    }

    private function insertAdministrator()
    {
        $query = '
            select
                piIsErrorOut as "hasError",
                pcErrCodeOut as "errorCode",
                pcErrMsgOut as "errorMessage",
                piOperatorOut as "id"
            from cyberft.p_member_api_create_main_admin(
                :piMember,
                :pcOperatorName,
                :pcPosition,
                :pcPhone,
                :pcEmail
            )
        ';

        return Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':piMember'       => $this->member_id,
                    ':pcOperatorName' => $this->operator_name,
                    ':pcPosition'     => $this->operator_position,
                    ':pcPhone'        => $this->phone,
                    ':pcEmail'        => $this->email,
                ]
            )
            ->queryOne();
    }

    private function insertOperator()
    {
        $query = '
            select
                piIsErrorOut as "hasError",
                pcErrCodeOut as "errorCode",
                pcErrMsgOut as "errorMessage",
                piOperatorOut as "id"
            from cyberft.p_member_api_create_operator(
                :piMember,
                :pcOperatorName,
                :piRole,
                :piFullPrivs,
                :piTerminal,
                :pcPosition,
                :pcPhone,
                :pcEmail,
                :pcExternalCode
            )
        ';

        return Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':piMember'       => $this->member_id,
                    ':pcOperatorName' => $this->operator_name,
                    ':piRole'         => $this->role_id,
                    ':piFullPrivs'    => 1,
                    ':piTerminal'     => $this->terminal_id,
                    ':pcPosition'     => $this->operator_position,
                    ':pcPhone'        => $this->phone,
                    ':pcEmail'        => $this->email,
                    ':pcExternalCode' => $this->external_code,
                ]
            )
            ->queryOne();
    }

    protected function executeUpdate()
    {
        $query = '
            select
                piIsErrorOut as "hasError",
                pcErrCodeOut as "errorCode",
                pcErrMsgOut as "errorMessage"
            from cyberft.p_member_api_update_operator(
                :piOperator,
                :piMember,
                :pcOperatorName,
                :piRole,
                :piFullPrivs,
                :piTerminal,
                :pcPosition,
                :pcPhone,
                :pcEmail,
                :pcExternalCode
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':piOperator'     => $this->operator_id,
                    ':piMember'       => $this->member_id,
                    ':pcOperatorName' => $this->operator_name,
                    ':piRole'         => $this->role_id,
                    ':piFullPrivs'    => 1,
                    ':piTerminal'     => $this->terminal_id,
                    ':pcPosition'     => $this->operator_position,
                    ':pcPhone'        => $this->phone,
                    ':pcEmail'        => $this->email,
                    ':pcExternalCode' => $this->external_code,
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
            from cyberft.p_member_api_delete_operator(:piOperator, 1)
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [':piOperator' => $this->operator_id]
            )
            ->queryOne();

        return $this->processDeleteQueryResult($result);
    }
}
