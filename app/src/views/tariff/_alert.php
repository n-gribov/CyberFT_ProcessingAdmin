<?php

use yii\helpers\Html;

$alertTypes = [
    'error'   => 'alert-danger',
    'danger'  => 'alert-danger',
    'success' => 'alert-success',
    'info'    => 'alert-info',
    'warning' => 'alert-warning'
];

$session = Yii::$app->session;
$flashes = $session->getAllFlashes();

foreach ($flashes as $type => $flash) {
    if (!isset($alertTypes[$type])) {
        continue;
    }

    foreach ((array) $flash as $i => $message) {
        $classes = ['alert', $alertTypes[$type]];
        echo Html::tag('div', $message, ['class' => $classes]);
    }

    $session->removeFlash($type);
}

?>