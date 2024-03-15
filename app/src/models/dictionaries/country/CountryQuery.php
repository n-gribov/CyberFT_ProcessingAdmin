<?php

namespace app\models\dictionaries\country;

/**
 * This is the ActiveQuery class for [[Country]].
 *
 * @see Country
 */
class CountryQuery extends \yii\db\ActiveQuery
{
    public function init()
    {
        $this->orderBy(['cntr_name' => SORT_ASC]);
        parent::init();
    }

    /**
     * {@inheritdoc}
     * @return Country[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Country|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
