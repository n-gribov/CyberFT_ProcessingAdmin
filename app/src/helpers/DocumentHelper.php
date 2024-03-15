<?php

namespace app\helpers;

use Yii;
use yii\helpers\ArrayHelper;

class DocumentHelper
{
    public static function getDocumentGroup()
    {
        $result = Yii::$app->db
                           ->createCommand('SELECT group_id, group_name FROM cyberft.v_doc_groups WHERE status=1')
                           ->queryAll();
        return ArrayHelper::map($result, 'group_id', 'group_name');
    }
}
