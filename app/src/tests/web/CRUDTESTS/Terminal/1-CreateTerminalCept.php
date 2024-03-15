<?php

$I = new AcceptanceTester($scenario);
$I->wantTo('Create terminal');

// вход в систему
$I->login();

// переходим на страницу Терминалы
$I->amOnPage('/terminal');
$I->waitForText('Терминалы', 10, 'h2');

//создание Терминала
$I->click('//a[@id=\'btn-create-data\']');
$I->waitForElementVisible ('//div[contains(@class, \'modal-header\')]');  //Проверяем, что модалка открылась
$I->SelectThis('//*[@id="data-form"]/div[1]/div/div/span/span[1]/span/span[2]','GITLAB CI SENDER'); // select2
$I->fillField ('//input[@id=\'terminal-terminal_name\']','Test-'.$I->random);  //Название
$I->fillField ('//input[@id=\'terminal-terminal_code\']', chr(mt_rand(65,90)));  //Код
// Остальные поля не особо важны, можно дописать позже
$I->click('//button[@id=\'btn-create-data-submit\']');  //Создаем
$I->waitForText('Терминал создан', 10);
