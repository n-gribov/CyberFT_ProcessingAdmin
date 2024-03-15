<?php

namespace app\models\participants\key;

use yii\db\ActiveQuery;

class KeyQuery extends ActiveQuery
{
    public function notDeleted()
    {
        return $this->andWhere(['not', ['status' => 0]]);
    }

    public function andFilterByQuery($query)
    {
        if (empty($query)) {
            return $this;
        }
        return $this->andWhere([
            'or',
            ['ilike', 'owner_name', $query],
            ['ilike', 'key_code', $query],
        ]);
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
