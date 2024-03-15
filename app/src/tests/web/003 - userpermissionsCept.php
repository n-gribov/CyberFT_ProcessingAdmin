<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Неавторизованному пользователю ничего недоступно');
$I->amOnPage('/login');
$I->seeCurrentUrlEquals('/site/login');
$I->amOnPage('/users');
$I->seeCurrentUrlEquals('/site/login');
$I->amOnPage('/babababababa/blalalalalla');
$I->seeCurrentUrlEquals('/site/login');