<?php

namespace app\models\participants\operator;

use yii\db\ActiveQuery;

class OperatorQuery extends ActiveQuery
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
            ['ilike', 'operator_name', $query],
            ['ilike', 'email', $query],
            ['ilike', 'swift_code', $query],
            ['ilike', 'member_name', $query],
            ['ilike', 'full_swift_code', $query],
            ['ilike', 'terminal_name', $query],
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
