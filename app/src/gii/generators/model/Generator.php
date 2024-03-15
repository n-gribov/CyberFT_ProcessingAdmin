<?php

namespace app\gii\generators\model;

/**
 * @property string $queryBaseClassWithoutNamespace
 * @property string $baseClassWithoutNamespace
 */
class Generator extends \yii\gii\generators\model\Generator
{
    public $baseClass = 'app\models\ActiveRecord';

    public function getName()
    {
        return 'Advanced Model Generator';
    }

    public function getBaseClassWithoutNamespace()
    {
        $reflectionClass = new \ReflectionClass($this->baseClass);
        return $reflectionClass->getShortName();
    }

    public function getQueryBaseClassWithoutNamespace()
    {
        $reflectionClass = new \ReflectionClass($this->queryBaseClass);
        return $reflectionClass->getShortName();
    }
}
