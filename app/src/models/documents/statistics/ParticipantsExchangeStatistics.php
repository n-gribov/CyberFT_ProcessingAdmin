<?php

namespace app\models\documents\statistics;


use app\models\documents\document\Document;

class ParticipantsExchangeStatistics
{
    public static function getTopSenders($days, $limit = 20)
    {
        return static::getTopParticipants('sender_swift_code', 'sender_name', $days, $limit);
    }

    public static function getTopReceivers($days, $limit = 20)
    {
        return static::getTopParticipants('receiver_swift_code', 'receiver_name', $days, $limit);
    }

    private static function getTopParticipants($swiftCodeColumn, $nameColumn, $days, $limit)
    {
        $startDate = (new \DateTime("$days days ago"))->format('Y-m-d');
        return Document::find()
            ->select(["$swiftCodeColumn as swiftCode", "$nameColumn as name", 'count(*)'])
            ->where(['>=', 'reg_time', $startDate])
            ->groupBy(['swiftCode', 'name'])
            ->orderBy(['count' => SORT_DESC])
            ->asArray()
            ->limit($limit)
            ->all();
    }
}
