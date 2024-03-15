<?php 

$I = new AcceptanceTester($scenario);
$I->wantTo('Dont see button for processing CYBERUM@EST');

// вход в систему
$I->login();

// переходим на страницу Участники
$I->amOnPage('/participant');

// проверка модального окна участника процессинга CYBERUM@EST на отсутствие кнопок
$I->click('//a[@data-sort="proc_swift_code"]');
$I->click('//*[@id="data-table"]/table/tbody/tr[1]/td[1]');
$I->waitForText('Участник', 10, 'h5');
$I->jswait();
$I->see('CYBERUM@EST', 'p');
$I->dontSeeElement('//*[@id="update-data"]');
$I->dontSeeElement('//*[@id="show_info"]/div[18]/div[2]/a');
$I->dontSeeElement('//*[@id="show_info"]/div[18]/div[3]/a');
$I->click('//*[@id="data-modal"]/div/div/div[1]/button/span');

