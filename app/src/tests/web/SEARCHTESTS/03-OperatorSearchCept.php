<?php
$I = new AcceptanceTester($scenario);
//CYB-4451
$I->wantTo('Check Operator Search');
$I->login();
$I->amOnPage ('/operator');
$I->waitForText ('Операторы','10');


//данные рандомные, при необходимости изменить
//ищем по имени оператора
$I->submitForm ('#w0',[
        'OperatorSearch[query]' => 'Контролер Процессинга CYBERUM@TEST'
    ]
);
$I->jswait ();
$I->waitForText ('CYBERUM@EST');
$I->waitForText ('Терминал процессинга CYBERUM@EST');

//данные рандомные, при необходимости изменить
//ищем по email оператора
$I->submitForm ('#w0',[
        'OperatorSearch[query]' => 'nikolashin1@cyberplat.com'
    ]
);
$I->jswait ();
$I->waitForText ('Николашин Антон Алексеевич');
$I->waitForText ('27.09.2018 17:19');
$I->seeNumberOfElements ('//tbody','1');

//данные рандомные, при необходимости изменить
//ищем по названию терминала
$I->submitForm ('#w0',[
        'OperatorSearch[query]' => 'Демо-Банк участник'
    ]
);
$I->jswait ();
$I->waitForText ('DEMONSTRATION BANK',10);
$I->see ('06.04.2020 17:32');

//данные рандомные, при необходимости изменить
//ищем по Swift-коду терминала
$I->jswait ();
$I->submitForm ('#w0',[
        'OperatorSearch[query]' => 'ZRNFRUM@AXXX'
    ]
);
$I->jswait ();
$I->waitForText ('АО «Зарубежнефть»',10);
$I->see ('06.04.2020 17:32');

//данные рандомные, при необходимости изменить
//ищем по названию участника
$I->waitForJS('return document.readyState == "complete";', 10);
$I->submitForm ('#w0',[
        'OperatorSearch[query]' => 'Провайдер CYBERUM'
    ]
);
$I->jswait ();
$I->waitForText ('Контролер Процессинга CYBERUM@TEST',10);
$I->see ('Терминал процессинга CYBERUM@EST');

//данные рандомные, при необходимости изменить
//ищем по Swift-коду участника
$I->waitForJS('return document.readyState == "complete";', 10);
$I->submitForm ('#w0',[
        'OperatorSearch[query]' => 'ZRNFRUM@XXX'
    ]
);
$I->jswait ();
$I->waitForText ('АО «Зарубежнефть»',10);
$I->see ('06.04.2020 17:32');