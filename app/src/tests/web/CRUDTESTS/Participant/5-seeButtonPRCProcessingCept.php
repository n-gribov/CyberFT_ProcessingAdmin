<?php 

$I = new AcceptanceTester($scenario);
$I->wantTo('See button for processing PSGTEST@PRC');

// вход в систему
$I->login();

// переходим на страницу Участники
$I->amOnPage('/participant');

// проверка модального окна участника процессинга PSGTEST@PRC на отображение кнопок
$I->click('//a[@data-sort="proc_swift_code"]');
$I->jswait();
$I->click('//a[@data-sort="-proc_swift_code"]');
$I->click('//*[@id="data-table"]/table/tbody/tr[3]/td[1]');
$I->waitForText('Участник', 10, 'h5');
$I->see('PSGTEST@PRC', 'p');
$I->seeElement('//*[@id="update-data"]');
$I->seeLink('Добавить терминал');
$I->seeLink('Заблокировать');
