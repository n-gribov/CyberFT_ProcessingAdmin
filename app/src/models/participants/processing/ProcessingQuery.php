<?php

namespace app\models\participants\processing;

use app\models\participants\ParticipantType;

/**
 * This is the ActiveQuery class for [[Processing]].
 *
 * @see Processing
 */
class ProcessingQuery extends \yii\db\ActiveQuery
{
    public function init()
    {
        $this->andOnCondition(['member_type' => ParticipantType::PROCESSING]);
        parent::init();
    }

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
            ['ilike', 'member_name', $query],
            ['ilike', 'swift_code', $query],
        ]);
    }

    /**
     * {@inheritdoc}
     * @return Processing[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Processing|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
