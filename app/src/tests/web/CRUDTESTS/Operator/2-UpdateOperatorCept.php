<?php

$I = new AcceptanceTester($scenario);
$I->wantTo('Update operator');

// вход в систему
$I->login();

// переходим на страницу Операторы
$I->amOnPage('/operator');

// изменение Оператора
$I->selectLastString();
$I->waitForText('Test', 10);
$I->see('!!!!2');
$I->see('Нет прав');
$I->jswait();
$I->waitForElementVisible('#update-data', 10);
$I->click('#update-data');
$I->waitForElementVisible('#operator-operator_name', 10);
$I->fillField('#operator-operator_name', '111_Test-'.$I->random);
$I->waitForElementVisible('#btn-update-data-submit', 10);
$I->click('#btn-update-data-submit');
$I->waitForText('Оператор сохранен', 10);

//CYB-4455 part 2 (base64)
// переходим на страницу Операторы
$I->amOnPage('/operator');
$I->selectLastString();
$I->waitForElementVisible ('//a[@id=\'update-data\']','10');
$OpName=($I->grabTextFrom('#show_info > div:nth-child(3) > div:nth-child(2) > p'));
$I->click('//div[contains(@class, \'pl-1\')]/a[contains(@class, \'btn btn-primary btn-sm btn-block\')]');
$I->waitForText ('Новый ключ','10');
$I->see ($OpName,'//span[@id=\'select2-uploadkeyform-ownerid-container\']');
$I->attachFile ('//input[@id=\'uploadkeyform-keybodyfile\']','/certs/upload_parse/pem.cer');
$I->click('//button[@id=\'btn-update-data-submit\']');
$I->jswait ();
$I->waitForElementVisible ('//button[@id=\'btn-create-data-submit\']','10');
$I->seeInField ('//input[@id=\'createkeyform-code\']','11111111A111-F415A05E3D92B05E971E64445BDE74420F21C159');
$I->seeInField ('//input[@id=\'createkeyform-startdate\']','01.08.2018');
$I->seeInField ('//input[@id=\'createkeyform-enddate\']','01.11.2018');
$I->click ('//button[@id=\'btn-create-data-submit\']');
$I->waitForText ('Ключ создан','10');
