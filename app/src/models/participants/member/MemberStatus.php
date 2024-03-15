<?php

namespace app\models\participants\member;

use Yii;

class MemberStatus
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_RESERVED = 2;
    const STATUS_NOT_DELETED = 3;

    public static function getStatusFilterOptions()
    {
        return [
            static::STATUS_DELETED     => Yii::t('app/participant', 'Deleted'),
            static::STATUS_ACTIVE      => Yii::t('app/participant', 'Active'),
            static::STATUS_RESERVED    => Yii::t('app/participant', 'Reserved'),
            static::STATUS_NOT_DELETED => Yii::t('app/participant', 'Not deleted'),
        ];
    }
}
