<?php

namespace app\controllers;

use app\models\documents\statistics\DocumentsDeliveryStatistics;
use app\models\documents\statistics\ParticipantsExchangeStatistics;
use Yii;

class StatisticsController extends BaseController
{
    public function actionDocuments()
    {
        $deliveryStatusStatsPeriods = [
            DocumentsDeliveryStatistics::PERIOD_TODAY,
            DocumentsDeliveryStatistics::PERIOD_YESTERDAY,
            DocumentsDeliveryStatistics::PERIOD_CURRENT_MONTH,
            DocumentsDeliveryStatistics::PERIOD_PREVIOUS_MONTH,
        ];

        $deliveryStatusStats = array_map(
            function ($period) {
                return new DocumentsDeliveryStatistics($period);
            },
            $deliveryStatusStatsPeriods
        );

        $participantsStatsDays = 365;
        $topSenders = ParticipantsExchangeStatistics::getTopSenders($participantsStatsDays);
        $topReceivers = ParticipantsExchangeStatistics::getTopReceivers($participantsStatsDays);

        Yii::$app->gon->push('topSenders', $topSenders);
        Yii::$app->gon->push('topReceivers', $topReceivers);

        return $this->render('documents', compact('deliveryStatusStats', 'participantsStatsDays'));
    }

    public function actionNetwork()
    {
        return $this->render('network');
    }
}
