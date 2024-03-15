<?php
$I = new AcceptanceTester($scenario);
//CYB-4451
$I->wantTo('Check Participant Search');
$I->login();
$I->amOnPage ('/participant');
$I->waitForText ('Участники','10');

//данные рандомные, при необходимости изменить
//ищем по названию
$I->submitForm ('#w0',[
    'ParticipantSearch[query]' => '(kg)'
    ]
);
$I->waitForText ('AIYLKG22XXX',10);
$I->see ('йылБанк (kg) Тест Николашин');
$I->seeNumberOfElements ('//tbody','1');

//данные рандомные, при необходимости изменить
//ищем по Swift-коду
$I->submitForm ('#w0',[
        'ParticipantSearch[query]' => 'PLATRUMMXXX'
    ]
);
$I->waitForText ('Терминал Платина',20);
$I->see ('11.01.2022 17:44');
$I->seeNumberOfElements ('//tbody','1');