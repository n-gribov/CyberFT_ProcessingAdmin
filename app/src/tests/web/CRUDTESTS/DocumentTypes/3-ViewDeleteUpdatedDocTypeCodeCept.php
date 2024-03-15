<?php
$I = new AcceptanceTester($scenario);
//CYB-4525
$I->wantTo('View updated document type and delete it');
$I->login();
$I->amOnPage ('/document-type');
try {
    $I->waitForText('Типы документов', '10', '//h2');
} catch(Exception $e) {
}

//просмотр последнего созданного типа
//в журнале
$I->SortByLastTime();
$I->See(($I->getData('UpdateDocTypeCode')),'//body//tbody//tr[1]/td[1]');
$I->see('Testing Update Document Type','//body//tbody//tr[1]/td[2]');
$I->see('Нет','//body//tbody//tr[1]/td[3]');
$I->see('Нет','//body//tbody//tr[1]/td[4]');
$I->see('EDM-RUS','//body//tbody//tr[1]/td[5]');
$I->see(($I->getData('DocTypeCodeCreateTime')),'//body//tbody//tr[1]/td[6]');


//просмотр в модалке
$I->click('//*[@id="data-table"]/table/tbody/tr[1]/td[1]');
$I->waitForElementChange('//div[contains(@class, \'modal-body\')]',function(Facebook\WebDriver\Remote\RemoteWebElement $el) {
    return $el->isDisplayed();
}, 10);
$I->waitForElementChange('//div[contains(@class, \'modal-body\')]',function(Facebook\WebDriver\Remote\RemoteWebElement $el) {
    return $el->isDisplayed();
}, 10);
$I->see('Тип документа','//div[@id=\'data-modal\']//h5[contains(@class,\'modal-title\')]');
$I->seeElement('//p[contains(text(), "' . ($I->getData('UpdateDocTypeCode')) . '")]');
$I->seeElement(' //p[contains(text(),\'Testing Update Document Type\')]');
$I->seeElement(' //p[contains(text(),\'EDM-RUS\')]');
$I->seeElement(' //p[contains(text(),\'SwiftFin\')]');
$I->seeElement('//p[contains(text(), "' . ($I->getData('DocTypeCodeCreateTime')) . '")]');
$I->see('Тарификация:
               Нет','//div[@id=\'show_info\']//div[1]//div[5]');
$I->see('Реестр:
               Нет','//div[@id=\'show_info\']//div[1]//div[6]');

$I->see(AcceptanceTester::ADMIN_LOGIN,'//div[contains(@class,\'modal-body\')]//div[2]//div[2]//div[2]//p[1]');
$I->see(AcceptanceTester::ADMIN_LOGIN,'//div[contains(@class,\'modal-body\')]//div[2]//div[4]//div[2]//p[1]');

//удаление
$I->click('//a[contains(@class, \'btn btn-danger btn-sm float-right\')]');
$I->acceptPopup();

try {
    $I->waitForText('Тип документа удален', '10', '//div[contains(@class, \'mt-3 alert alert-success\')]');
} catch(Exception $e) {
}

//в журнале его нет
$I->SortByLastTime();
$I->dontSee(($I->getData('UpdateDocTypeCode')),'//body//tbody//tr[1]/td[1]');

$I->amOnPage('/logout');