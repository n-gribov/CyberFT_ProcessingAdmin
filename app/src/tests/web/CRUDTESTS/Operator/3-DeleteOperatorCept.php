<?php 

$I = new AcceptanceTester($scenario);
$I->wantTo('Delete operator');

// вход в систему
$I->login();

// переходим на страницу Операторы
$I->amOnPage('/operator');

// удаление Оператора
$I->selectLastString();
$I->see('111_Test-');
$I->see('!!!!2');
$I->see('Нет прав');
$I->waitForElementVisible('#show_info > div.d-flex.mt-3 > div.ml-auto > a', 10);
$I->click('#show_info > div.d-flex.mt-3 > div.ml-auto > a');
$I->acceptPopup();
$I->waitForText('Оператор удален', 10);