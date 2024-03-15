<?php

$I = new AcceptanceTester($scenario);
//CYB-4451
$I->wantTo('Check Participant Search');
$I->login();
$I->amOnPage ('/participant');
$I->waitForText ('Участники','10');


//Поиск по названию
$I->applyPageFilter ('//*[@id="data-table-filters"]/td[1]/input','GITLAB');
$I->waitForText ('GITLAB CI RECEIVER',10);
$I->see ('GITLAB CI SENDER');
$I->seeNumberOfElements ('//tbody/tr/td[1]','3');
$I->clearPageFilter ('//*[@id="data-table-filters"]/td[1]/input');


//Поиск по коду
$I->applyPageFilter ('//*[@id="data-table-filters"]/td[2]/input','GITLBCITEST');
$I->waitForText ('Тестовый процессинг postgresql',10);
$I->seeNumberOfElements ('//tbody/tr/td[1]',1);
$I->clearPageFilter ('//*[@id="data-table-filters"]/td[2]/input');


//Поиск по названию процессинга
$I->applyPageFilter ('//*[@id="data-table-filters"]/td[7]/input','Тестовый процессинг postgresql');
$I->waitForText ('GITLAB CI SENDER',10);
$I->clearPageFilter ('//*[@id="data-table-filters"]/td[7]/input');


//Поиск по коду процессинга
$I->applyPageFilter ('//*[@id="data-table-filters"]/td[8]/input','PSGTEST@PRC');
$I->waitForText ('TESTTERMXXX',10);
//$I->see('TESTTERMXXX');
$I->clearPageFilter ('//*[@id="data-table-filters"]/td[8]/input');
$I->wait(5);


//Кредитная организация или нет
$I->selectOption ('//*[@id="data-table-filters"]/td[3]/select','1');
$I->reloadPage ();
$I->waitForText ('AGUNRUM@MLT','10');
$I->selectOption ('//*[@id="data-table-filters"]/td[3]/select','');
$I->jswait ();

//Удален или нет или нет
$I->selectOption ('//*[@id="data-table-filters"]/td[4]/select','0');
$I->reloadPage ();
$I->waitForText ('SKOLKOVO','10');
$I->selectOption ('//*[@id="data-table-filters"]/td[4]/select','');

//Заблокирован или нет
$I->selectOption ('//*[@id="data-table-filters"]/td[5]/select','1');
$I->reloadPage ();
$I->waitForText ('ООО "Сольди"','10');
$I->selectOption ('//*[@id="data-table-filters"]/td[5]/select','');

//поиск по дате
$I->fillField ('//input[@id=\'participantsearch-i_date\']','25.09.2018');
$I->reloadPage ();
$I->waitForText ('ЕВРАЗ ЗСМК');