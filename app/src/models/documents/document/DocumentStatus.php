<?php

namespace app\models\documents\document;

use Yii;

class DocumentStatus
{
    const ACCEPTED        = 11; // Принят
    const CHECK_ERROR     = 12; // Ошибка при проверке
    const QUEUED          = 13; // Поставлен в очередь на отправку
    const WAIT_CONFIRM    = 14; // Ожидание подтверждения
    const DELIVERED       = 15; // Доставлен следующему узлу
    const SEND_ERROR      = 16; // Ошибка при доставке
    const FINAL_DELIVERED = 17; // Доставлен получателю
    const ERROR_DELIVERED = 18; // Доставлен получателю с ошибкой
    const NOT_DELIVERED   = 19; // Не доставлен
    const READY_TO_SEND   = 31; // Ожидает отправки
    const IN_PROCESS      = 32; // Отправляется
    const WAS_SEND        = 33; // Отправлено
    const RECEIVED        = 34; // Получено
    const ERROR_QUEUING   = 37; // Ошибка постановки в очередь
    const ERROR_SENDING   = 38; // Ошибка отправки
    const SUSPENDED       = 39; // Приостановлено

    private static $names;

    public static function isResendable($status): bool
    {
        return in_array(
            $status,
            [
                static::DELIVERED,
                static::SEND_ERROR,
                static::FINAL_DELIVERED,
                static::ERROR_DELIVERED,
                static::NOT_DELIVERED,
                static::WAS_SEND,
                static::RECEIVED,
                static::ERROR_QUEUING,
                static::ERROR_SENDING,
                static::SUSPENDED,
            ]
        );
    }

    public static function isSuspendable($status): bool
    {
        return in_array(
            $status,
            [
                static::ACCEPTED,
                static::QUEUED,
                static::WAIT_CONFIRM,
                static::READY_TO_SEND,
                static::IN_PROCESS,
                static::WAS_SEND,
                static::RECEIVED,
            ]
        );
    }

    public static function all(): array
    {
        static::loadNames();
        return self::$names;
    }

    public static function getName($id)
    {
        static::loadNames();
        return self::$names[$id] ?? null;
    }

    private static function loadNames()
    {
        if (static::$names === null) {
            static::$names = [
                self::ACCEPTED        => Yii::t('app/document-status', 'Принят'),
                self::CHECK_ERROR     => Yii::t('app/document-status', 'Ошибка при проверке'),
                self::QUEUED          => Yii::t('app/document-status', 'Поставлен в очередь на отправку'),
                self::WAIT_CONFIRM    => Yii::t('app/document-status', 'Ожидание подтверждения'),
                self::DELIVERED       => Yii::t('app/document-status', 'Доставлен следующему узлу'),
                self::SEND_ERROR      => Yii::t('app/document-status', 'Ошибка при доставке'),
                self::FINAL_DELIVERED => Yii::t('app/document-status', 'Доставлен получателю'),
                self::ERROR_DELIVERED => Yii::t('app/document-status', 'Доставлен получателю с ошибкой'),
                self::NOT_DELIVERED   => Yii::t('app/document-status', 'Не доставлен'),
                self::READY_TO_SEND   => Yii::t('app/document-status', 'Ожидает отправки'),
                self::IN_PROCESS      => Yii::t('app/document-status', 'Отправляется'),
                self::WAS_SEND        => Yii::t('app/document-status', 'Отправлено'),
                self::RECEIVED        => Yii::t('app/document-status', 'Получено'),
                self::ERROR_QUEUING   => Yii::t('app/document-status', 'Ошибка постановки в очередь'),
                self::ERROR_SENDING   => Yii::t('app/document-status', 'Ошибка отправки'),
                self::SUSPENDED       => Yii::t('app/document-status', 'Приостановлено'),
            ];
        }
    }
}
