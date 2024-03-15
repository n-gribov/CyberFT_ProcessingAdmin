<?php

$I = new AcceptanceTester($scenario);
$I->wantTo('Create operator');

// вход в систему
$I->login();

// переходим на страницу Операторы
$I->amOnPage('/operator');

// создание нового Оператора
$I->click('Создать оператора');
$I->waitForElementVisible ('//div[contains(@class, \'modal-header\')]');
$I->waitForText('Новый оператор', 10, 'h5');
$I->jswait();
$I->SelectThis('//*[@id="data-form"]/div[1]/div/div/span/span[1]/span/span[2]','!!!!2'); //select2
$I->selectOption('Терминал', 'New');
$I->fillField('Имя', 'Test-'.$I->random);
$OpName=($I->grabValueFrom('#operator-operator_name'));
$I->selectOption('Роль', 'Нет прав');
$I->click('Создать');
$I->waitForText('Оператор создан, добавить ключ', '10');

//CYB-4464
$I->seeElement ('//div[contains(@class, \'mt-3 alert alert-success\')]/a');
$I->click ('//div[contains(@class, \'mt-3 alert alert-success\')]/a');
$I->waitForElementVisible ('//*[@id="data-modal"]/div/div/div[1]',10);
$I->see('Новый ключ');
$I->see ($OpName);

//CYB-4455
$I->attachFile ('//input[@id=\'uploadkeyform-keybodyfile\']','/certs/upload_parse/der.cer');
$I->click('//button[@id=\'btn-update-data-submit\']');
$I->jswait ();
$I->waitForElementVisible ('//button[@id=\'btn-create-data-submit\']','10');
$I->seeInField ('//input[@id=\'createkeyform-code\']','11111111A111-542FF06BD1CF063CC0E580F1D8A1F707D34DE5A9');
$I->seeInField ('//input[@id=\'createkeyform-startdate\']','01.08.2018');
$I->seeInField ('//input[@id=\'createkeyform-enddate\']','01.11.2018');
$I->click ('//button[@id=\'btn-create-data-submit\']');
$I->waitForText ('Ключ создан','10');

