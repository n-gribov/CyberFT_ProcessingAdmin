<?php

namespace app\models\documents\documentType;

use yii\db\ActiveQuery;

class DocumentTypeQuery extends ActiveQuery
{
    public function notDeleted()
    {
        return $this->andWhere(['not', ['status' => 0]]);
    }

    public function all($db = null)
    {
        return parent::all($db);
    }

    public function one($db = null)
    {
        return parent::one($db);
    }
}
