<?php

namespace app\i18n;

class Formatter extends \yii\i18n\Formatter
{
    public function asBoolean($value)
    {
        return $value ? $this->booleanFormat[1] : $this->booleanFormat[0];
    }
}
