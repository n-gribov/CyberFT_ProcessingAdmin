<?php

$I = new AcceptanceTester($scenario);
//CYB-4458
$I->wantTo('Check Document Search');
$I->login();
$I->amOnPage ('/document');
$I->waitForText ('Документы','10','//h2');


//Поиск по id
$I->applyPageFilter ('//*[@id="documents-table-filters"]/td[2]/input','1234');
$I->waitForText ('COSMRUMAXXXX',10);
$I->see ('Терминал для тестирования входящих сообщений','//*[@id="documents-table"]/table/tbody/tr/td[7]');
$I->seeNumberOfElements ('//*[@id="documents-table"]//tbody/tr','1');
$I->clearPageFilter ('//*[@id="documents-table-filters"]/td[2]/input');

//поиск по типу документа
//@todo select не работает
//$I->SelectThis('//*[@id="documents-table-filters"]/td[2]/span/span[1]/span','MT999');
//$I->waitForText ('DEMONSTRATION BANK',10);
//$I->seeElement('//td[contains(text(),\'70883110-34E7-11E9-8EB3-0242AC110002\')]');
//$I->click('//span[contains(@class, \'select2-selection__clear\')]');

//@todo код отправитель получатель код получателя id статус время
$I->amOnPage('/logout');