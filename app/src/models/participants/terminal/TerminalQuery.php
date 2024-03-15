<?php

namespace app\models\participants\terminal;

/**
 * This is the ActiveQuery class for [[Terminal]].
 *
 * @see Terminal
 */
class TerminalQuery extends \yii\db\ActiveQuery
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
            ['ilike', 'member_swift_code', $query],
            ['ilike', 'member_name', $query],
            ['ilike', 'full_swift_code', $query],
            ['ilike', 'terminal_name', $query],
        ]);
    }

    /**
     * {@inheritdoc}
     * @return Terminal[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Terminal|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
