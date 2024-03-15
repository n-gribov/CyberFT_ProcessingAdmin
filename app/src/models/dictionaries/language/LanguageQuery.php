<?php

namespace app\models\dictionaries\language;

/**
 * This is the ActiveQuery class for [[Language]].
 *
 * @see Language
 */
class LanguageQuery extends \yii\db\ActiveQuery
{
    public function init()
    {
        $this->orderBy(['lang_name' => SORT_ASC]);
        parent::init();
    }

    /**
     * {@inheritdoc}
     * @return Language[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Language|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
