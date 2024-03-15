<?php
$I = new AcceptanceTester($scenario);
//CYB-4457
$I->wantTo('Check Documents Link in Top Level Menu');
$I->login();
$I->click('//*[@id="menuContent"]/ul/li[2]/a');
$I->waitForText ('Документы','10');
$I->seeCurrentUrlEquals ('/document');