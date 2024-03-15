<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('see user menu');
$I->login();
$I->click('Администратор');
$I->seeElement('//*[@id="menuContent"]/ul/li[1]/div');
$I->click('Пользователи');
$I->seeCurrentUrlEquals('/user');
$I->see('Пользователи');
$I->click('Идентификатор');
$I->click('#data-table > table > tbody > tr:nth-child(2) > td:nth-child(2)');
$I->waitForElementVisible('//div[contains(@class, \'modal-header\')]','20');
$I->see('Администратор CFT');
$I->see('Создатель:');
$I->see('cyberft');
//Кнопка редактировать
$I->seeElement('//a[@id=\'update-data\']');
//Кнопка удалить
$I->seeElement ('//a[contains(@class, \'btn btn-danger btn-sm float-right\')]');
$I->click('//*[@id="menuContent"]/ul/li[7]/a');