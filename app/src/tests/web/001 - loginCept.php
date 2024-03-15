<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('login');
$I->amonpage('/site/login');
$I->seecurrenturlequals('/site/login');
$I->see('Вход в систему');

$I->seeElement('#w0 > div.form-label-group.field-loginform-username.required');
$I->seeElement('#w0 > div.form-label-group.field-loginform-password.required');

$I->fillfield('//input[@id=\'loginform-username\']',AcceptanceTester::ADMIN_LOGIN);
$I->fillfield('//input[@id=\'loginform-password\']',AcceptanceTester::ADMIN_PASSWORD);
$I->click('Войти');
$I->seeElement('//div[@id=\'menuContent\']');
$I->click('//*[@id="menuContent"]//li[8]/a');
$I->seeCurrentUrlEquals('/site/login');
$I->see('Вход в систему');