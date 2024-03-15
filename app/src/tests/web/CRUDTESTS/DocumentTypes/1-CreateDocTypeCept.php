<?php
$I = new AcceptanceTester($scenario);
//CYB-4525
$I->wantTo('Add document type');
$I->login();
$I->amOnPage ('/document-type');
try {
    $I->waitForText('Типы документов', '10', '//h2');
} catch(Exception $e) {
}

//создаем
$I->click('//a[@id=\'btn-create-data\']');

//создание в модалке
$I->waitForElementChange('//div[contains(@class, \'modal-body\')]',function(Facebook\WebDriver\Remote\RemoteWebElement $el) {
    return $el->isDisplayed();
}, 10);
$I->see('Новый тип документа','//div[@id=\'data-modal\']//h5[contains(@class,\'modal-title\')]');
$I->fillField('Код', 'TestDocumentType-'.uniqid('', true));
$I->fillField('Название','Testing Create Document Type');
$I->selectOption('//select[@id=\'documenttype-group_id\']','Общая группа');
$I->selectOption('//select[@id=\'documenttype-is_register\']','Да');
$I->selectOption('Тарификация','Да');

//сохраняем, ждем подтверждения
$I->click('Создать');
try {
    $I->waitForText('Тип документа создан', '10', '//div[contains(@class, \'mt-3 alert alert-success\')]');
} catch(Exception $e) {
}

$I->SortByLastTime();

//сохраняем тестовые данные
$I->saveData('DocTypeCode', ($I->grabTextFrom('//body//tbody//tr[1]/td[1]')));
$I->saveData('DocTypeCodeCreateTime', ($I->grabTextFrom('//body//tbody//tr[1]/td[1]/following-sibling::*[5]')));


$I->amOnPage('/logout');