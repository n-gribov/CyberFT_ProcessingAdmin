<?php
$I = new AcceptanceTester($scenario);
//CYB-4456
$I->wantTo('Check Participant Menu');
$I->login();
$I->click('//*[@id="menuContent"]/ul/li[3]/a');
$I->click('//div[contains(@class, \'dropdown-menu shadow-lg show\')]/a[contains(@class, \'dropdown-item\')][1]');
$I->waitForText ('Участники','10');
$I->seeCurrentUrlEquals ('/participant');

$I->click('//*[@id="menuContent"]/ul/li[3]/a');
$I->click('//div[contains(@class, \'dropdown-menu shadow-lg show\')]/a[contains(@class, \'dropdown-item\')][2]');
$I->waitForText ('Терминалы','10');
$I->seeCurrentUrlEquals ('/terminal');

$I->click('//*[@id="menuContent"]/ul/li[3]/a');
$I->click('//div[contains(@class, \'dropdown-menu shadow-lg show\')]/a[contains(@class, \'dropdown-item\')][3]');
$I->waitForText ('Операторы','10');
$I->seeCurrentUrlEquals ('/operator');

$I->click('//*[@id="menuContent"]/ul/li[3]/a');
$I->click('//div[contains(@class, \'dropdown-menu shadow-lg show\')]/a[contains(@class, \'dropdown-item\')][4]');
$I->waitForText ('Ключи','10');
$I->seeCurrentUrlEquals ('/key');