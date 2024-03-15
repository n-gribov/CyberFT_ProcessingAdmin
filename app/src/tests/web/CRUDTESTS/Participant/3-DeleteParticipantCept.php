<?php 

$I = new AcceptanceTester($scenario);
$I->wantTo('Delete participant');

// вход в систему
$I->login();

// переходим на страницу Участники
$I->amOnPage('/participant');

// удаление Участника
$I->selectLastString();
$I->waitForText('Участник', 10, 'h5');
$I->see('111_Test-', 'p');
$I->see('12345678', 'p');
$I->click('Заблокировать');
$I->fillField('//input[contains(@class, \'form-control form-control-sm\')]','тест блокировки');
$I->waitForText('Заблокировать', 10);
$I->click('Заблокировать');
$I->waitForText('Участник заблокирован', 10);
$I->selectLastString();
$I->waitForText('Участник', 10, 'h5');
$I->see('111_Test-', 'p');
$I->see('12345678', 'p');
$I->click('Удалить');
$I->acceptPopup();
$I->waitForText('Участник удален', 10);
