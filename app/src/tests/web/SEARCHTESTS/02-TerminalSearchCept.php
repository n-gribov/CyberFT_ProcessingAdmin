<?php
$I = new AcceptanceTester($scenario);
//CYB-4451
$I->wantTo('Check Terminal Search');
$I->login();
$I->amOnPage ('/terminal');
$I->waitForText ('Терминалы','10');


//данные рандомные, при необходимости изменить
//ищем по названию терминала
$I->submitForm ('#w0',[
        'TerminalSearch[query]' => 'процессинг'
    ]
);
$I->jswait ();
$I->waitForText ('CYBERUM@EST',10);
$I->see ('24.04.2019 15:20');
$I->seeNumberOfElements ('//tbody','1');

//данные рандомные, при необходимости изменить
//ищем по Swift-коду терминала
$I->submitForm ('#w0',[
        'TerminalSearch[query]' => 'PLATRUMMAXXX'
    ]
);
$I->jswait ();
$I->waitForText ('Терминал Платина','20');
$I->waitForText ('11.01.2022 17:44','20');

//данные рандомные, при необходимости изменить
//ищем по названию участника
$I->waitForJS('return document.readyState == "complete";', 10);
$I->submitForm ('#w0',[
        'TerminalSearch[query]' => 'Провайдер CYBERUM'
    ]
);
$I->jswait ();
$I->waitForText ('CYBERUM@EST','20');
$I->see ('24.04.2019 15:20');

//данные рандомные, при необходимости изменить
//ищем по Swift-коду участника
$I->waitForJS('return document.readyState == "complete";', 10);
$I->submitForm ('#w0',[
        'TerminalSearch[query]' => 'DEMORUS@XXX'
    ]
);
$I->jswait ();
$I->waitForText ('DEMONSTRATION BANK','20');
$I->see ('Демо-Банк участник');