<?php

namespace app\models\dictionaries\language;

use app\models\ActiveRecord;
use Yii;

/**
 * This is the model class for table "cyberft.w_languages".
 *
 * @property int $lang_num
 * @property string $lang_name
 */
class Language extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cyberft.v_languages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lang_num'], 'default', 'value' => null],
            [['lang_num'], 'integer'],
            [['lang_name'], 'string', 'max' => 125],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lang_num' => Yii::t('app/language', 'Lang Num'),
            'lang_name' => Yii::t('app/language', 'Lang Name'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return LanguageQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LanguageQuery(get_called_class());
    }

    protected function executeInsert()
    {
        // TODO: Implement executeInsert() method.
    }

    protected function executeUpdate()
    {
        // TODO: Implement executeUpdate() method.
    }

    protected function executeDelete()
    {
        // TODO: Implement executeDelete() method.
    }
}
