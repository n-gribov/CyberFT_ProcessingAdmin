<?php

namespace app\models\participants\terminal;

use Yii;

class TerminalWorkMode
{
    public static function all()
    {
        return [
            0 => Yii::t('app/terminal', 'No signatures'),
            1 => Yii::t('app/terminal', 'One signature'),
            2 => Yii::t('app/terminal', 'Two signatures'),
        ];
    }
}
