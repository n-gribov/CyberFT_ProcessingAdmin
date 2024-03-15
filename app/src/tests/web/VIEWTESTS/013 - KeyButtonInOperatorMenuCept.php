<?php
//CYB-4463
$I = new AcceptanceTester($scenario);
$I->wantTo('Check Key Button In Operator Update Modal');

// вход в систему
$I->login();

// переходим на страницу Операторы
$I->amOnPage('/operator');

$I->selectLastString();
$I->waitForText ('Аудит',10);
$I->jswait ();
//запоминаем id оператора
$OpName=($I->grabTextFrom ('#show_info > div:nth-child(3) > div:nth-child(2) > p'));

//кликаем на кнопку "Ключи"
$I->click('//*[@id="data-modal"]/div/div/div[2]/div/nav/a');
$I->waitForText ('Ключи',10);
$I->see('Ключи оператора ' . $OpName);