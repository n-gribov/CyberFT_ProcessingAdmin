<?php

use yii\helpers\Url;

?>

<nav class="navbar navbar-expand navbar-dark bg-dark pt-0 pb-0 mlr-15">
    <a class="navbar-brand" href="#">
        <img src="/img/cyberFT.svg" alt="CyberFT" width="50">
    </a>

    <div class="collapse navbar-collapse" id="menuContent">
        <ul class="navbar-nav">

            <li class="nav-item dropdown ">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><?=Yii::t('app', 'Administrator')?> </a>
                <div class="dropdown-menu shadow-lg">
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-fw fa-calendar-alt"></i> <?=Yii::t('app', 'Date viewed')?></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?=Url::to('/user')?>">
                        <i class="fas fa-fw fa-user-friends"></i> <?=Yii::t('app', 'Users')?></a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-fw fa-exchange-alt"></i> <?=Yii::t('app', 'Roles')?></a>
                    <a class="dropdown-item" href="<?=Url::to(['/sys-param'])?>">
                        <i class="fas fa-fw fa-cogs"></i> <?=Yii::t('app', 'System parametrs')?></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-fw fa-keyboard"></i> <?=Yii::t('app', 'Change password')?></a>
                    <a class="dropdown-item" href="<?=Url::to(['site/logout'])?>" data-method='POST'>
                        <i class="fas fa-fw fa-sign-in-alt"></i> <?=Yii::t('app', 'Logout')?></a>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="/document"><?=Yii::t('app', 'Documents')?></a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><?=Yii::t('app', 'Participants')?></a>
                <div class="dropdown-menu shadow-lg">
                    <a class="dropdown-item" href="/participant">
                        <i class="fas fa-fw fa-book"></i> <?=Yii::t('app', 'Participants')?></a>
                    <a class="dropdown-item" href="/terminal">
                        <i class="fas fa-fw fa-desktop"></i> <?=Yii::t('app', 'Participants terminals')?></a>
                    <a class="dropdown-item" href="/operator">
                        <i class="fas fa-fw fa-address-card"></i> <?=Yii::t('app', 'Staff')?></a>
                    <a class="dropdown-item" href="/key">
                        <i class="fas fa-fw fa-key"></i> <?=Yii::t('app', 'Keys')?></a>
                    <a class="dropdown-item" href="/processing">
                        <i class="fas fa-fw fa-server"></i> <?=Yii::t('app', 'Processings')?></a>
                </div>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><?=Yii::t('app', 'Dictionaries')?></a>
                <div class="dropdown-menu shadow-lg">
                    <a class="dropdown-item" href="/routing">
                        <i class="fas fa-fw fa-code-branch"></i> <?=Yii::t('app', 'Routing')?></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-fw fa-file-alt"></i> <?=Yii::t('app', 'Participants contracts')?></a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-fw fa-print"></i> <?=Yii::t('app', 'Document scans')?></a>
                    <a class="dropdown-item" href="/document-type">
                        <i class="fas fa-fw fa-book"></i> <?=Yii::t('app', 'Document types')?></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-fw fa-money-bill-wave"></i> <?=Yii::t('app', 'Currencies')?></a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-fw fa-flag"></i> <?=Yii::t('app', 'Countries')?></a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-fw fa-map-marker-alt"></i> <?=Yii::t('app', 'Cities')?></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-fw fa-book"></i> <?=Yii::t('app', 'Internal dictionaries')?></a>
                </div>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><?=Yii::t('app', 'Logs')?></a>
                <div class="dropdown-menu shadow-lg">
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-fw fa-book"></i> <?=Yii::t('app', 'Errors log')?></a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-fw fa-book"></i> <?=Yii::t('app', 'Update export log')?></a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-fw fa-book"></i> <?=Yii::t('app', 'Update import log')?></a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-fw fa-book"></i> <?=Yii::t('app', 'User activity log')?></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-fw fa-cogs"></i> <?=Yii::t('app', 'Configurations')?></a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-fw fa-info-circle"></i> <?=Yii::t('app', 'Information')?></a>
                </div>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><?=Yii::t('app', 'Statistics')?></a>
                <div class="dropdown-menu shadow-lg">
                    <a class="dropdown-item" href="/statistics/documents">
                        <i class="fas fa-fw fa-chart-bar"></i> <?=Yii::t('app', 'Documents exchange statistics')?>
                    </a>
                    <a class="dropdown-item" href="/statistics/network">
                        <i class="fas fa-fw fa-chart-line"></i> <?=Yii::t('app', 'Network statistics')?>
                    </a>
                </div>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><?=Yii::t('app', 'Help')?></a>
                <div class="dropdown-menu shadow-lg">
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-fw fa-question-circle"></i> <?=Yii::t('app', 'Documentation')?></a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-fw fa-info-circle"></i> <?=Yii::t('app', 'About')?></a>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="<?=Url::to(['site/logout'])?>" data-method='POST'>
                    <i class="fas fa-fw fa-sign-in-alt"></i> <?=Yii::t('app', 'Logout')?>
                </a>
            </li>
        </ul>
    </div>
</nav>
