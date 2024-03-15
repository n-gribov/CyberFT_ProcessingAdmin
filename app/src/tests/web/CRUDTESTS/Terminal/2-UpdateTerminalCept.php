<?php

$I = new AcceptanceTester($scenario);
$I->wantTo('Update terminal');

// вход в систему
$I->login();

// переходим на страницу Терминалы
$I->amOnPage('/terminal');
$I->waitForText('Терминалы', 10, 'h2');

// обновляем запись Терминала
$I->selectLastString();
$I->waitForElementVisible('//*[@id="data-modal"]/div/div/div[1]');
$I->waitForText('Терминал');
$I->see('Test-');
$I->see('GITLAB CI SENDER');
$I->click('#update-data');
$I->waitForElementVisible('//*[@id="data-modal"]/div/div/div[1]');
$I->waitForText('Редактирование терминала');
$I->fillField ('//input[@id=\'terminal-terminal_name\']', '111_Test-'.$I->random);
$I->click('//*[@id="btn-update-data-submit"]');
$I->waitForText('Терминал сохранен', 10);
