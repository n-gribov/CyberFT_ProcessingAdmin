<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;


   /**
    * Define custom actions here
    */

    const ADMIN_LOGIN = 'cfta';
    const ADMIN_PASSWORD ='Ay-k_6yELRG';
    public $random = 0;


    // срабатывает в начале теста
    public function _initialize()
    {
        $this->random = mt_rand(100,999);
    }


    public function login()
    {
        $this->random = mt_rand(1,1000);

        $I = $this;
        $I->wantTo('login');
        $I->amonpage('/site/login');
        $I->seecurrenturlequals('/site/login');
        $I->see('Вход в систему');
        $I->fillfield('//input[@id=\'loginform-username\']',AcceptanceTester::ADMIN_LOGIN);
        $I->fillfield('//input[@id=\'loginform-password\']',AcceptanceTester::ADMIN_PASSWORD);
        $I->click('Войти');
        try {
            $I->waitForElement('//div[@id=\'menuContent\']', 20);
            $I->seeCurrentUrlEquals('/site/login');
            $I->see('Вход в систему');
        } catch (Exception $e) {
        }
    }


    public function jswait()
    {
        $I = $this;
        $I->waitForJS('return document.readyState == "complete";', 10);
        $I->waitForJS("return $.active == 0;", 10);
    }


    /**
     *
     */
    public function SortByLastTime()
    {
        $I = $this;
        $I->click('//a[@data-sort="i_date"]');
        $I->jswait();
        $I->click('//a[@data-sort="-i_date"]');
        $I->jswait();
    }

    public function selectLastString()  // кликаем по последней записи в таблице
    {
        $I = $this;
        $I->click('//a[@data-sort="i_date"]');
        $I->waitForJS('return document.readyState == "complete";', 10);
        $I->waitForJS("return $.active == 0;", 10);
        $I->click('//a[@data-sort="-i_date"]');
        $I->click('//*[@id="data-table"]/table/tbody/tr[1]/td[1]');

    }

    /**
     * @param $xpath
     * @param $text
     * @throws Exception
     */
    public function applyPageFilter($xpath, $text)
    {
        $cnt = 0;
        $I = $this;
        while(true) {
        $I->waitForElementVisible($xpath,10);
        $I->waitForElementChange($xpath,function(Facebook\WebDriver\Remote\RemoteWebElement $xpath) {
            return $xpath->isDisplayed();
        }, 10);
            try {
                $I->fillField ($xpath,$text);
                $I->waitForElementChange($xpath,function(Facebook\WebDriver\Remote\RemoteWebElement $xpath) {
                    return $xpath->isDisplayed();
                }, 10);
                $I->pressKey ($xpath, Facebook\WebDriver\WebDriverKeys::ENTER);
                $I->waitForElementChange($xpath,function(Facebook\WebDriver\Remote\RemoteWebElement $xpath) {
                    return $xpath->isDisplayed();
                }, 10);
                $I->wait(0.5);
                $I->seeInField ($xpath, $text);
                $I->jswait();
                break;
            } catch (\Exception $ex) {
                if ($cnt < 30) {
                    $cnt++;
                } else {
                    throw $ex;
                }

            }
        }

    }

    /**
     * @param $xpath
     * @throws Exception
     */
    public function clearPageFilter($xpath)
    {
        $cnt = 0;
        $I = $this;
        while(true) {
            $I->fillField ($xpath,'');
            try {
                $I->pressKey ($xpath, Facebook\WebDriver\WebDriverKeys::ENTER);
                $I->waitForElementChange($xpath,function(Facebook\WebDriver\Remote\RemoteWebElement $xpath) {
                    return $xpath->isDisplayed();
                }, 10);
                $I->waitForElementVisible ($xpath,10);
                $I->seeInField ($xpath,'');
                $I->jswait();
                break;
            } catch (\Exception $ex) {
                if ($cnt < 30) {
                    $cnt++;
                } else {
                    throw $ex;
                }
            }
        }
    }

    /**
     * function for select2 vendor
     * @param $xpath
     * @param $text
     * @throws Exception
     */
    public function SelectThis($xpath , $text)
    {
        $I = $this;
        $I->waitForElement($xpath,'10');
        $I->jswait ();
        $I->click($xpath);
        $I->waitForElementChange($xpath, function(Facebook\WebDriver\Remote\RemoteWebElement $element) {
            return $element->isDisplayed();
        }, 5);
        $I->dontSeeElement('//li[contains(@class, \'select2-results__option select2-results__message\')]');
        $I->fillField('//input[contains(@class, \'select2-search__field\')]',$text);
        $I->waitForElementChange('//li[contains(@class, \'select2-results__option\')]', function(Facebook\WebDriver\Remote\RemoteWebElement $element) {
            return $element->isDisplayed();
        }, 5);
        $I->click('//li[contains(@class, \'select2-results__option select2-results__option--highlighted\') and contains(text(),' . '"' . "$text" . '"' . ')]');
        $I->waitForElementChange('//span[@class=\'select2-selection__rendered\']', function(Facebook\WebDriver\Remote\RemoteWebElement $element) {
            return $element->isDisplayed();
        }, 5);
        $I->waitForElementVisible('//span[contains(@title,' . '"' . "$text" . '"' . ') or contains(text(),' . '"' . "$text" . '"' . ') and @class=\'select2-selection__rendered\']','5');
        $I->waitForText($text,'10', $xpath . '/preceding-sibling::*[1]');
    }

    /**
     * @param $key
     * @param $value
     */

    public function saveData($key, $value)
    {
        $data = unserialize(file_get_contents('tests/_data/data_values'));
        $data[$key] = $value;
        file_put_contents('tests/_data/data_values', serialize($data));
    }

    /**
     * @param $key
     * @return mixed
     */

    public function getData($key)
    {
        $data = unserialize(file_get_contents('tests/_data/data_values'));
        return $data[$key];
    }


}