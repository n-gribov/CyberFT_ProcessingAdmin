<?php

namespace app\models\dictionaries\country;

use app\models\ActiveRecord;
use Yii;

/**
 * This is the model class for table "cyberft.v_country".
 *
 * @property string $cntr_code2
 * @property string $cntr_code3
 * @property string $cntr_num
 * @property string $cntr_code_full
 * @property string $cntr_name
 */
class Country extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cyberft.v_country';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cntr_code2', 'cntr_code3', 'cntr_num'], 'string', 'max' => 20],
            [['cntr_code_full'], 'string', 'max' => 80],
            [['cntr_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cntr_code2' => Yii::t('app/county', 'Cntr Code2'),
            'cntr_code3' => Yii::t('app/county', 'Cntr Code3'),
            'cntr_num' => Yii::t('app/county', 'Cntr Num'),
            'cntr_code_full' => Yii::t('app/county', 'Cntr Code Full'),
            'cntr_name' => Yii::t('app/county', 'Cntr Name'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return CountryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CountryQuery(get_called_class());
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
