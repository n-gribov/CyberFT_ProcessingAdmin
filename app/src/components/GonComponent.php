<?php

namespace app\components;

use yii\helpers\Json;

class GonComponent extends \ijackua\gon\GonComponent
{
    public function gonScriptWrapped()
    {
        $serializedData = $this->gonScript();
        $jsVariable = "window.{$this->jsVariableName}";
        return "$jsVariable = Object.assign($jsVariable || {}, $serializedData);";
    }

    public function gonScript()
    {
        $allVariables = $this->allVariables();
        return empty($allVariables)
            ? '{}'
            : Json::encode($allVariables);
    }
}
