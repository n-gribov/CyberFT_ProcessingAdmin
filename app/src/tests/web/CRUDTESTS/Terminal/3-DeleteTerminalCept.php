<?php

$I = new AcceptanceTester($scenario);
$I->wantTo('Delete terminal');

// вход в систему
$I->login();

// переходим на страницу Терминалы
$I->amOnPage('/terminal');
$I->waitForText('Терминалы', 10, 'h2');

// обновляем запись Терминала
$I->selectLastString();
$I->waitForElementVisible('//*[@id="data-modal"]/div/div/div[1]');
$I->waitForText('Терминал');
$I->see('111_Test-');
$I->see('GITLAB CI SENDER');
$I->click('Заблокировать');
$I->fillField('//input[contains(@class, \'form-control form-control-sm\')]','тест блокировки');
$I->click('Заблокировать');
$I->waitForText('Терминал заблокирован', 10);
$I->selectLastString();
$I->waitForText('Участник', 10);
$I->see('GITLAB CI SENDER');
$I->click('Удалить');
$I->acceptPopup();
$I->waitForText('Терминал удален', 10);