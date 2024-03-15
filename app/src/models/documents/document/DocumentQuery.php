<?php

namespace app\models\documents\document;

use yii\db\ActiveQuery;

class DocumentQuery extends ActiveQuery
{
    public function andFilterByQuery($query)
    {
        if (empty($query)) {
            return $this;
        }
        return $this->andWhere([
            'or',
            ['ilike', 'message_code', $query],
            ['ilike', 'message_name', $query],
            ['ilike', 'sender_msg_code', $query],
            ['ilike', 'sender_swift_code', $query],
            ['ilike', 'sender_name', $query],
            ['ilike', 'receiver_swift_code', $query],
            ['ilike', 'receiver_name', $query],
            ['ilike', 'snd_full_swift_code', $query],
            ['ilike', 'snd_terminal_name', $query],
            ['ilike', 'rsv_full_swift_code', $query],
            ['ilike', 'rsv_terminal_name', $query],
        ]);
    }

    public function successfullyDelivered(): self
    {
        return $this->andWhere(['send_status' => [DocumentStatus::DELIVERED, DocumentStatus::FINAL_DELIVERED]]);
    }

    public function all($db = null)
    {
        return parent::all($db);
    }

    public function one($db = null)
    {
        return parent::one($db);
    }
}