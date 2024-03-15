<?php

namespace app\models\participants\participant;

use app\models\participants\ParticipantType;
use app\models\participants\processing\Processing;

/**
 * This is the ActiveQuery class for [[Participant]].
 *
 * @see Participant
 */
class ParticipantQuery extends \yii\db\ActiveQuery
{
    public function init()
    {
        $this->andOnCondition(['member_type' => ParticipantType::PARTICIPANT]);
        parent::init();
    }

    public function notDeleted()
    {
        return $this->andWhere(['not', ['status' => 0]]);
    }

    public function belongsToPrimaryProcessing()
    {
        $processingQuery = Processing::find()
            ->notDeleted()
            ->andWhere(['is_primary' => 1])
            ->select('member_id');
        return $this->andWhere(['in', 'parent_id', $processingQuery]);
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
     * @return Participant[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Participant|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
