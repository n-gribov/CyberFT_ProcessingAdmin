<?php

namespace app\models\documents\document;

use app\models\ActiveRecord;
use Yii;
use yii\base\NotSupportedException;

/**
 * This is the model class for table "cyberft.v_messages".
 *
 * @property int $message_id
 * @property int $message_type_id
 * @property string $message_code
 * @property string $message_name
 * @property int $command_id
 * @property int $sender_id
 * @property string $sender_code
 * @property string $sender_swift_code
 * @property string $sender_name
 * @property int $receiver_id
 * @property string $receiver_code
 * @property string $receiver_swift_code
 * @property string $receiver_name
 * @property string $sender_msg_code
 * @property int $status
 * @property int $send_status
 * @property string $send_status_name
 * @property int $is_error
 * @property string $reg_time
 * @property string $finish_time
 * @property int $send_cnt
 * @property int $send_ok
 * @property int $send_err
 * @property int $message_body
 * @property string $message_hash
 * @property string $err_code
 * @property string $err_msg
 * @property int $message_length
 * @property string $message_sum
 * @property int $snd_terminal_id
 * @property string $snd_terminal_code
 * @property string $snd_full_swift_code
 * @property string $snd_terminal_name
 * @property int $rsv_terminal_id
 * @property string $rsv_terminal_code
 * @property string $rsv_full_swift_code
 * @property string $rsv_terminal_name
 * @property int $message_cnt
 * @property int $curr_id
 * @property string $curr_code
 * @property string $curr_num
 * @property string $curr_name
 * @property int $format_id
 * @property string $format_code
 * @property string $format_name
 * @property string $final_time
 * @property string $del_time
 * @property string $del_user
 * @property string $del_msg
 * @property int $next_terminal_id
 * @property string $next_swift_code
 * @property string $next_terminal_name
 * @property string $next_member_name
 */
class Document extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cyberft.v_messages';
    }

    public static function primaryKey()
    {
        return ['message_id'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['message_id', 'message_type_id', 'command_id', 'sender_id', 'receiver_id', 'status', 'send_status', 'is_error', 'send_cnt', 'send_ok', 'send_err', 'message_body', 'message_length', 'snd_terminal_id', 'rsv_terminal_id', 'message_cnt', 'curr_id', 'format_id', 'next_terminal_id'], 'default', 'value' => null],
            [['message_id', 'message_type_id', 'command_id', 'sender_id', 'receiver_id', 'status', 'send_status', 'is_error', 'send_cnt', 'send_ok', 'send_err', 'message_body', 'message_length', 'snd_terminal_id', 'rsv_terminal_id', 'message_cnt', 'curr_id', 'format_id', 'next_terminal_id'], 'integer'],
            [['reg_time', 'finish_time', 'final_time', 'del_time'], 'safe'],
            [['message_hash'], 'string'],
            [['message_sum'], 'number'],
            [['message_code', 'sender_code', 'sender_swift_code', 'receiver_code', 'receiver_swift_code', 'sender_msg_code', 'snd_full_swift_code', 'rsv_full_swift_code', 'del_user', 'next_swift_code'], 'string', 'max' => 80],
            [['message_name', 'sender_name', 'receiver_name', 'send_status_name', 'err_msg', 'snd_terminal_name', 'rsv_terminal_name', 'curr_name', 'format_name', 'del_msg', 'next_terminal_name', 'next_member_name'], 'string', 'max' => 255],
            [['err_code', 'snd_terminal_code', 'rsv_terminal_code', 'curr_code', 'curr_num', 'format_code'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'message_id' => Yii::t('app/document', 'ID'),
            'message_type_id' => Yii::t('app/document', 'Message type ID'),
            'message_code' => Yii::t('app/document', 'Message type code'),
            'message_name' => Yii::t('app/document', 'Message type'),
            'command_id' => Yii::t('app/document', 'Command ID'),
            'sender_id' => Yii::t('app/document', 'Sender ID'),
            'sender_code' => Yii::t('app/document', 'Sender code'),
            'sender_swift_code' => Yii::t('app/document', 'Sender Swift code'),
            'sender_name' => Yii::t('app/document', 'Sender'),
            'receiver_id' => Yii::t('app/document', 'Receiver ID'),
            'receiver_code' => Yii::t('app/document', 'Receiver code'),
            'receiver_swift_code' => Yii::t('app/document', 'Receiver Swift code'),
            'receiver_name' => Yii::t('app/document', 'Receiver'),
            'sender_msg_code' => Yii::t('app/document', 'Sender message ID'),
            'status' => Yii::t('app/document', 'Status'),
            'send_status' => Yii::t('app/document', 'Sending status ID'),
            'send_status_name' => Yii::t('app/document', 'Sending status'),
            'is_error' => Yii::t('app/document', 'Is error'),
            'reg_time' => Yii::t('app/document', 'Registration time'),
            'finish_time' => Yii::t('app/document', 'Next node delivery time'),
            'send_cnt' => Yii::t('app/document', 'Deliveries count'),
            'send_ok' => Yii::t('app/document', 'Successful deliveries count'),
            'send_err' => Yii::t('app/document', 'Failed deliveries count'),
            'message_body' => Yii::t('app/document', 'Message body'),
            'message_hash' => Yii::t('app/document', 'Message content hash'),
            'err_code' => Yii::t('app/document', 'Error code'),
            'err_msg' => Yii::t('app/document', 'Error message'),
            'message_length' => Yii::t('app/document', 'Message length'),
            'message_sum' => Yii::t('app/document', 'Amount'),
            'snd_terminal_id' => Yii::t('app/document', 'Sender terminal ID'),
            'snd_terminal_code' => Yii::t('app/document', 'Sender terminal code'),
            'snd_full_swift_code' => Yii::t('app/document', 'Sender terminal Swift code'),
            'snd_terminal_name' => Yii::t('app/document', 'Sender terminal'),
            'rsv_terminal_id' => Yii::t('app/document', 'Receiver terminal ID'),
            'rsv_terminal_code' => Yii::t('app/document', 'Receiver terminal code'),
            'rsv_full_swift_code' => Yii::t('app/document', 'Receiver terminal Swift code'),
            'rsv_terminal_name' => Yii::t('app/document', 'Receiver terminal'),
            'message_cnt' => Yii::t('app/document', 'Documents count'),
            'curr_id' => Yii::t('app/document', 'Currency ID'),
            'curr_code' => Yii::t('app/document', 'Currency code'),
            'curr_num' => Yii::t('app/document', 'Currency numeric code'),
            'curr_name' => Yii::t('app/document', 'Currency'),
            'format_id' => Yii::t('app/document', 'Format ID'),
            'format_code' => Yii::t('app/document', 'Format code'),
            'format_name' => Yii::t('app/document', 'Format'),
            'final_time' => Yii::t('app/document', 'Receiver delivery time'),
            'del_time' => Yii::t('app/document', 'Deletion time'),
            'del_user' => Yii::t('app/document', 'Deleted by user'),
            'del_msg' => Yii::t('app/document', 'Deletion message'),
            'next_terminal_id' => Yii::t('app/document', 'Next terminal ID'),
            'next_swift_code' => Yii::t('app/document', 'Next terminal Swift code'),
            'next_terminal_name' => Yii::t('app/document', 'Next terminal'),
            'next_member_name' => Yii::t('app/document', 'Next member'),
        ];
    }

    public static function find()
    {
        return new DocumentQuery(get_called_class());
    }

    public function isSuspendable(): bool
    {
        return DocumentStatus::isSuspendable($this->send_status);
    }

    public function isResendable(): bool
    {
        return DocumentStatus::isResendable($this->send_status);
    }

    public function updateStatus($newStatus): bool
    {
        $query = '
            select
                piIsErrorOut as "hasError",
                pcErrCodeOut as "errorCode",
                pcErrMsgOut as "errorMessage"
            from cyberft.p_message_api_set_msg_status(
                :piMessage,
                :piSendStatus
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':piMessage'    => $this->message_id,
                    ':piSendStatus' => $newStatus,
                ]
            )
            ->queryOne();

        $updatedRowsCount = $this->processRecordChangeQueryResult($result, 'status update');
        $this->refresh();

        return $updatedRowsCount === 1;
    }

    public function resend(): bool
    {
        return $this->updateStatus(DocumentStatus::READY_TO_SEND);
    }

    public function getDefaultVisibleColumns()
    {
        return [
            'message_id',
            'message_name',
            'sender_swift_code',
            'sender_name',
            'receiver_swift_code',
            'receiver_name',
            'send_status_name',
            'reg_time:datetime',
            'final_time',
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
