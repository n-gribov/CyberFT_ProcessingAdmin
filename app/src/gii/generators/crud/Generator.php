<?php

namespace app\gii\generators\crud;

use app\helpers\UserHelper;
use yii\gii\generators\crud\Generator as BaseGenerator;
use Yii;
use yii\gii\CodeFile;
use yii\web\View;

/**
 * @property mixed $modelClassWithoutNamespace
 */
class Generator extends BaseGenerator
{
    public $baseControllerClass = 'app\controllers\BaseController';

    public function init()
    {
        parent::init();

        $data = [
            'username' => Yii::$app->user->identity->username,
            'password' => Yii::$app->user->identity->password
        ];

        UserHelper::setDBData($data);
    }

    public function getModelClassWithoutNamespace()
    {
        $reflectionClass = new \ReflectionClass($this->modelClass);
        return $reflectionClass->getShortName();
    }

    public function formView()
    {
        return \Yii::getAlias('@vendor/yiisoft/yii2-gii/src/generators/crud/form.php');
    }

    public function stickyAttributes()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $controllerFile = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->controllerClass, '\\')) . '.php');

        $files = [
            new CodeFile($controllerFile, $this->render('controller.php')),
        ];

        if (!empty($this->searchModelClass)) {
            $searchModel = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->searchModelClass, '\\') . '.php'));
            $files[] = new CodeFile($searchModel, $this->renderSearchModel());
        }

        $viewPath = $this->getViewPath();
        $templatePath = $this->getTemplatePath() . '/views';
        foreach (scandir($templatePath) as $file) {
            if (empty($this->searchModelClass) && $file === '_search.php') {
                continue;
            }
            if (is_file($templatePath . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $files[] = new CodeFile("$viewPath/$file", $this->render("views/$file"));
            }
        }

        return $files;
    }

    public function renderSearchModel()
    {
        $view = new View();
        $params['generator'] = $this;
        $templatePath = '@vendor/yiisoft/yii2-gii/src/generators/crud/default/search.php';

        return $view->renderFile($templatePath, $params, $this);
    }
}
