<?php
$I = new AcceptanceTester($scenario);
//CYB-4451
$I->wantTo('Check Key Search');
$I->login();
$I->amOnPage ('/key');
$I->waitForText ('Ключи','10');


//данные рандомные, при необходимости изменить
//ищем по коду ключа
$I->waitForJS('return document.readyState == "complete";', 10);
$I->submitForm ('#w0',[
        'KeySearch[query]' => '524525C5FE5ADE9A47A34D345DBB0567FC8B6724'
    ]
);
$I->jswait ();
$I->waitForText ('DEMORUM@AXXX-524525C5FE5ADE9A47A34D345DBB0567FC8B6724',10);
$I->see ('06.04.2020');

//данные рандомные, при необходимости изменить
//ищем по email оператора
$I->waitForJS('return document.readyState == "complete";', 10);
$I->submitForm ('#w0',[
        'KeySearch[query]' => 'Контролер процессинга PSGTEST@APRC'
    ]
);
$I->jswait ();
$I->waitForText ('PSGTEST@APRC-900924C49EC6EC8488180F92A0E35EC7A0AB59AD',10);