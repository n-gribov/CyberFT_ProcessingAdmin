<?php

namespace app\models\participants\terminal;

use app\models\ActiveRecord;
use Yii;

/**
 * This is the model class for table "cyberft.v_terminals".
 *
 * @property int $terminal_id
 * @property int $member_id
 * @property string $member_swift_code
 * @property string $member_name
 * @property int $member_type
 * @property string $terminal_code
 * @property string $full_swift_code
 * @property string $terminal_name
 * @property int $status
 * @property string $status_name
 * @property int $work_mode
 * @property string $work_mode_name
 * @property int $blocked
 * @property string $block_info
 * @property string $i_date
 * @property string $i_user
 * @property string $u_date
 * @property string $u_user
 * @property string $d_date
 * @property string $d_user
 * @property string $login_param
 * @property string $login_addr
 * @property int $fragment_ready
 * @property int $fragment_size
 * @property int $queue_length
 */
class Terminal extends ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public const STATUS_DELETED = 0;

    public static function tableName()
    {
        return 'cyberft.v_terminals';
    }

    public static function primaryKey()
    {
        return ['terminal_id'];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $assignableAttributes = [
            'terminal_code', 'terminal_name', 'login_param', 'login_addr',
            'work_mode', 'fragment_ready', 'fragment_size', 'queue_length',
        ];
        $scenarios[self::SCENARIO_CREATE] = array_merge($assignableAttributes, ['member_id']);
        $scenarios[self::SCENARIO_UPDATE] = $assignableAttributes;
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['member_id', 'work_mode', 'fragment_ready', 'fragment_size', 'queue_length'], 'default', 'value' => null],
            [['member_id', 'work_mode', 'fragment_ready', 'fragment_size', 'queue_length'], 'integer'],
            [['terminal_name', 'login_param', 'login_addr'], 'string', 'max' => 255],
            [['terminal_code'], 'string', 'max' => 1],
            [['member_id', 'terminal_name', 'terminal_code', 'work_mode'], 'required']
        ];
    }

    public function attributeLabels()
    {
        return [
            'terminal_id' => Yii::t('app/model', 'ID'),
            'member_id' => Yii::t('app/terminal', 'Member ID'),
            'member_swift_code' => Yii::t('app/terminal', 'Participant Swift code'),
            'member_name' => Yii::t('app/terminal', 'Member name'),
            'member_type' => Yii::t('app/terminal', 'Member type'),
            'terminal_code' => Yii::t('app/model', 'Code'),
            'full_swift_code' => Yii::t('app/participant', 'Swift code'),
            'terminal_name' => Yii::t('app/model', 'Name'),
            'status' => Yii::t('app/model', 'Status'),
            'status_name' => Yii::t('app/model', 'Status'),
            'work_mode' => Yii::t('app/terminal', 'Work mode'),
            'work_mode_name' => Yii::t('app/terminal', 'Work mode'),
            'blocked' => Yii::t('app/model', 'Blocked'),
            'block_info' => Yii::t('app/model', 'Blocking reason'),
            'i_date' => Yii::t('app/model', 'Created at'),
            'i_user' => Yii::t('app/model', 'Created by'),
            'u_date' => Yii::t('app/model', 'Updated at'),
            'u_user' => Yii::t('app/model', 'Updated by'),
            'd_date' => Yii::t('app/model', 'Deleted at'),
            'd_user' => Yii::t('app/model', 'Deleted by'),
            'login_param' => Yii::t('app/terminal', 'Login parameters'),
            'login_addr' => Yii::t('app/terminal', 'Network address'),
            'fragment_ready' => Yii::t('app/terminal', 'Messages fragmentation support'),
            'fragment_size' => Yii::t('app/terminal', 'Fragment size'),
            'queue_length' => Yii::t('app/terminal', 'Queue length'),
        ];
    }

    public static function find()
    {
        return new TerminalQuery(get_called_class());
    }

    protected function executeInsert()
    {
        $query = '
            select
                piIsErrorOut as "hasError",
                pcErrCodeOut as "errorCode",
                pcErrMsgOut as "errorMessage",
                piTerminalOut as "id"
            from cyberft.p_member_api_create_terminal(
                :piMember,
                :pcTerminalCode,
                :pcTerminalName,
                :piWorkMode,
                :pcLoginParam,
                :pcLoginAddr,
                :piFragmentReady,
                :piFragmentSize,
                :piQueueLength
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':piMember'        => $this->member_id,
                    ':pcTerminalCode'  => $this->terminal_code,
                    ':pcTerminalName'  => $this->terminal_name,
                    ':piWorkMode'      => $this->work_mode,
                    ':pcLoginParam'    => $this->login_param,
                    ':pcLoginAddr'     => $this->login_addr,
                    ':piFragmentReady' => $this->fragment_ready,
                    ':piFragmentSize'  => $this->fragment_size,
                    ':piQueueLength'   => $this->queue_length,
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
            from cyberft.p_member_api_update_terminal(
                :piTerminal,
                :pcTerminalCode,
                :pcTerminalName,
                :piWorkMode,
                :pcLoginParam,
                :pcLoginAddr,
                :piFragmentReady,
                :piFragmentSize,
                :piQueueLength
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':piTerminal'      => $this->terminal_id,
                    ':pcTerminalCode'  => $this->terminal_code,
                    ':pcTerminalName'  => $this->terminal_name,
                    ':piWorkMode'      => $this->work_mode,
                    ':pcLoginParam'    => $this->login_param,
                    ':pcLoginAddr'     => $this->login_addr,
                    ':piFragmentReady' => $this->fragment_ready,
                    ':piFragmentSize'  => $this->fragment_size,
                    ':piQueueLength'   => $this->queue_length,
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
            from cyberft.p_member_api_delete_terminal(:piTerminal, 1)
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [':piTerminal' => $this->terminal_id]
            )
            ->queryOne();

        return $this->processDeleteQueryResult($result);
    }

    public function setBlockStatus(bool $isBlocked, $reason): bool
    {
        $query = '
            select
                piiserrorout as "hasError",
                pcerrcodeout as "errorCode",
                pcerrmsgout as "errorMessage"
            from cyberft.p_member_api_block_terminal(
                :piMember,
                :piBlock,
                :pcBlockInfo
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    'piMember'    => $this->getPrimaryKey(),
                    'piBlock'     => $isBlocked ? 1 : 0,
                    'pcBlockInfo' => $reason,
                ]
            )
            ->queryOne();

        return $this->processRecordChangeQueryResult($result, $isBlocked ? 'block' : 'unblock') !== false;
    }
}
