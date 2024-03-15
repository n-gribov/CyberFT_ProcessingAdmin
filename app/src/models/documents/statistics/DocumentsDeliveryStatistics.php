<?php

namespace app\models\documents\statistics;

use app\models\documents\document\Document;
use Yii;
use yii\helpers\ArrayHelper;

class DocumentsDeliveryStatistics
{
    const STATUS_DELIVERED = 'delivered';
    const STATUS_PENDING = 'pending';
    const STATUS_FAILED = 'failed';
    const STATUS_UNKNOWN = 'unknown';

    const PERIOD_TODAY = 'today';
    const PERIOD_YESTERDAY = 'yesterday';
    const PERIOD_CURRENT_MONTH = 'current_month';
    const PERIOD_PREVIOUS_MONTH = 'previous_month';

    public $deliveredCount = 0;
    public $pendingCount = 0;
    public $failedCount = 0;

    private static $startDateTimeConstructorParam = [
        self::PERIOD_TODAY => 'today',
        self::PERIOD_YESTERDAY => 'yesterday',
        self::PERIOD_CURRENT_MONTH => 'first day of this month',
        self::PERIOD_PREVIOUS_MONTH => 'first day of previous month',
    ];

    private static $endDateTimeConstructorParam = [
        self::PERIOD_TODAY => 'tomorrow',
        self::PERIOD_YESTERDAY => 'today',
        self::PERIOD_CURRENT_MONTH => 'first day of next month',
        self::PERIOD_PREVIOUS_MONTH => 'first day of this month',
    ];

    private $period;
    private $startDate;
    private $endDate;

    public function __construct($period)
    {
        $this->period = $period;
        $this->initializeDates();
        $this->loadData();
    }

    public function getPeriodLabel()
    {
        switch ($this->period) {
            case static::PERIOD_TODAY:
                return Yii::t('app', 'Today');
            case static::PERIOD_YESTERDAY:
                return Yii::t('app', 'Yesterday');
            case static::PERIOD_CURRENT_MONTH:
                return Yii::t('app', 'This month');
            case static::PERIOD_PREVIOUS_MONTH:
                $date = new \DateTime('first day of previous month');
                $month = Yii::t('app', $date->format('F'));
                $year = $date->format('Y');
                return "$month $year";
            default:
                return null;
        }
    }

    private function loadData()
    {
        $countByCategory = \Yii::$app->cache->getOrSet(
            $this->createCacheKey(),
            function () { return $this->fetchData(); },
            $this->getCacheDuration()
        );

        $this->deliveredCount = $countByCategory[static::STATUS_DELIVERED];
        $this->pendingCount = $countByCategory[static::STATUS_PENDING];
        $this->failedCount = $countByCategory[static::STATUS_FAILED];
    }

    private function fetchData()
    {
        $counts = Document::find()
            ->select(['send_status', 'count(*) as count'])
            ->where(['>=', 'reg_time', $this->startDate])
            ->andWhere(['<', 'reg_time', $this->endDate])
            ->groupBy('send_status')
            ->asArray()
            ->all();

        $countByStatus = ArrayHelper::map($counts, 'send_status', 'count');
        $countByCategory = [
            static::STATUS_DELIVERED => 0,
            static::STATUS_PENDING => 0,
            static::STATUS_FAILED => 0,
            static::STATUS_UNKNOWN => 0,
        ];
        foreach ($countByStatus as $status => $count) {
            $category = static::getStatusCategory($status);
            $countByCategory[$category] += $count;
        }
        return $countByCategory;
    }

    private function createCacheKey(): string
    {
        return implode('-', [__CLASS__, $this->startDate, $this->endDate]);
    }

    private function initializeDates()
    {
        $startDateConstructorParam = static::$startDateTimeConstructorParam[$this->period];
        $this->startDate = (new \DateTime($startDateConstructorParam))
            ->format('Y-m-d');

        $endDateConstructorParam = static::$endDateTimeConstructorParam[$this->period];
        $this->endDate = (new \DateTime($endDateConstructorParam))
            ->format('Y-m-d');
    }

    private function getCacheDuration()
    {
        switch ($this->period) {
            case static::PERIOD_TODAY:
            case static::PERIOD_CURRENT_MONTH:
                return 60;
            default:
                return 60 * 60;
        }
    }

    private static function getStatusCategory($status)
    {
        switch ($status) {
            case 15: // Доставлен следующему узлу
            case 17: // Доставлен получателю
                return static::STATUS_DELIVERED;
            case 12: // Ошибка при проверке
            case 16: // Ошибка при доставке
            case 18: // Доставлен получателю с ошибкой
                return static::STATUS_FAILED;
            case 11: // Принят
            case 13: // Поставлен в очередь на отправку
            case 14: // Ожидание подтверждения
            case 19: // Не доставлен
            case 31: // Ожидает отправки
            case 32: // Отправляется
            case 33: // Отправлено
            case 34: // Получено
            case 37: // Ошибка постановки в очередь
            case 38: // Ошибка отправки
            case 39: // Приостановлено
                return static::STATUS_PENDING;
            default:
                return static::STATUS_UNKNOWN;
        }
    }
}
