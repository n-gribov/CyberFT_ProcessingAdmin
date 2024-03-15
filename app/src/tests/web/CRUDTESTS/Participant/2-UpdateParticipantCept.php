<?php 

$I = new AcceptanceTester($scenario);
$I->wantTo('Update participant');

// вход в систему
$I->login();

// переходим на страницу Участники
$I->amOnPage('/participant');

//изменяем запись участника
$I->selectLastString();
$I->waitForText('Участник', 10, 'h5');
$I->see('Test-', 'p');
$I->see('12345678', 'p');
$I->click('Редактировать');
$I->waitForText('Редактирование участника', 10, 'h5');
$I->fillField('Название', '111_Test-'.$I->random);
$I->click('Сохранить');
$I->waitForText('Участник сохранен', 10);
