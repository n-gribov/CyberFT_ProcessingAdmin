<?php 

$I = new AcceptanceTester($scenario);
$I->wantTo('Сreate participant');

// вход в систему
$I->login();

// переходим на страницу Участники
$I->amOnPage('/participant');

// создание Участника
$I->click('Создать участника');
$I->waitForText('Новый участник', 10, 'h5');
$I->seeOptionIsSelected('Процессинг', 'Тестовый процессинг postgresql');
$I->fillField('Код', '12345678900');
$I->fillField('Название', 'Test-'.$I->random);
$I->selectOption('//select[@id=\'participant-is_bank\']','Нет');
$I->click('Создать');
// проверяем что после создания Участника открывается модалка создания Терминала для созданного участника
$I->jswait();
$I->waitForText('Новый терминал', 10, 'h5');
//$I->seeInField('//span[contains(@class, \'select2-selection select2-selection--single\')]', 'Test-');

