<?php
$I = new AcceptanceTester($scenario);
//CYB-4525
$I->wantTo('View and mpdify document type');
$I->login();
$I->amOnPage ('/document-type');
try {
    $I->waitForText('Типы документов', '10', '//h2');
} catch(Exception $e) {
}

//просмотр последнего созданного типа
//в журнале
$I->SortByLastTime();
$I->See(($I->getData('DocTypeCode')),'//body//tbody//tr[1]/td[1]');
$I->see('Testing Create Document Type','//body//tbody//tr[1]/td[2]');
$I->see('Да','//body//tbody//tr[1]/td[3]');
$I->see('Да','//body//tbody//tr[1]/td[4]');
$I->see('Общая группа','//body//tbody//tr[1]/td[5]');
$I->see(($I->getData('DocTypeCodeCreateTime')),'//body//tbody//tr[1]/td[6]');

//просмотр в модалке
$I->click('//*[@id="data-table"]/table/tbody/tr[1]/td[1]');
$I->waitForElementChange('//div[contains(@class, \'modal-body\')]',function(Facebook\WebDriver\Remote\RemoteWebElement $el) {
    return $el->isDisplayed();
}, 10);
$I->wait(5);
$I->see('Тип документа','//div[@id=\'data-modal\']//h5[contains(@class,\'modal-title\')]');
$I->seeElement('//p[contains(text(), "' . ($I->getData('DocTypeCode')) . '")]');
$I->seeElement(' //p[contains(text(),\'Testing Create Document Type\')]');
$I->seeElement(' //p[contains(text(),\'Общая группа\')]');
$I->seeElement(' //p[contains(text(),\'Общая система\')]');
$I->seeElement('//p[contains(text(), "' . ($I->getData('DocTypeCodeCreateTime')) . '")]');
$I->see('Тарификация:
               Да','//div[@id=\'show_info\']//div[1]//div[5]');
$I->see('Реестр:
               Да','//div[@id=\'show_info\']//div[1]//div[6]');

$I->see(AcceptanceTester::ADMIN_LOGIN,'//div[contains(@class,\'modal-body\')]//div[2]//div[2]//div[2]//p[1]');
//редактирование
$I->click('//a[@id=\'update-data\']');
$I->waitForElementChange('//div[contains(@class, \'modal-body\')]',function(Facebook\WebDriver\Remote\RemoteWebElement $el) {
    return $el->isDisplayed();
}, 10);
$I->see('Изменить тип документа','//div[@id=\'data-modal\']//h5[contains(@class,\'modal-title\')]');

//просто проверим кнопку
$I->click('//button[@id=\'btn-update-data-cancel\']');


//просмотр в модалке
$I->waitForElementChange('//div[contains(@class, \'modal-body\')]',function(Facebook\WebDriver\Remote\RemoteWebElement $el) {
    return $el->isDisplayed();
}, 10);
$I->see('Тип документа','//div[@id=\'data-modal\']//h5[contains(@class,\'modal-title\')]');

//редактирование
$I->click('//a[@id=\'update-data\']');
$I->waitForElementChange('//div[contains(@class, \'modal-body\')]',function(Facebook\WebDriver\Remote\RemoteWebElement $el) {
    return $el->isDisplayed();
}, 10);
$I->see('Изменить тип документа','//div[@id=\'data-modal\']//h5[contains(@class,\'modal-title\')]');
$I->fillField('Код', 'TestDocumentType-'.$I->random);
$I->fillField('Название','Testing Update Document Type');
$I->selectOption('//select[@id=\'documenttype-group_id\']','EDM-RUS');
$I->selectOption('//select[@id=\'documenttype-is_register\']','Нет');
$I->selectOption('//select[@id=\'documenttype-tariffication\']','Нет');
$I->click('//button[@id=\'btn-update-data-submit\']');

try {
    $I->waitForText('Тип документа обновлен', '10', '//div[contains(@class, \'mt-3 alert alert-success\')]');
} catch(Exception $e) {
}

$I->SortByLastTime();

$I->saveData('UpdateDocTypeCode', ($I->grabTextFrom('//body//tbody//tr[1]/td[1]')));

$I->amOnPage('/logout');