<?php

namespace app\models\participants\key\forms;

use app\models\participants\key\Key;
use Yii;
use yii\base\Model;

class UpdateKeyForm extends Model
{
    public $startDate;
    public $endDate;
    public $changeReason;

    private $keyId;
    private $lastDbErrorMessage;

    public static function create(Key $key): self
    {
        $form = new self();
        $form->keyId = $key->key_id;
        $form->setAttributes(
            [
                'startDate' => static::formatDate($key->start_date, 'd.m.Y'),
                'endDate' => static::formatDate($key->end_date, 'd.m.Y'),
            ],
            false
        );
        return $form;
    }

    private static function formatDate($dateString, $targetFormat)
    {
        if (empty($dateString)) {
            return null;
        }
        return (new \DateTime($dateString))->format($targetFormat);
    }

    public function attributeLabels()
    {
        return [
            'startDate' => Yii::t('app/key', 'Start date'),
            'endDate' => Yii::t('app/key', 'End date'),
            'changeReason' => Yii::t('app/key', 'Change reason'),
        ];
    }

    public function rules()
    {
        return [
            ['startDate', 'date', 'format' => 'php:d.m.Y'],
            ['changeReason', 'string', 'max' => 255],
            [['startDate', 'endDate', 'changeReason'], 'safe'],
        ];
    }

    public function save(Key $key)
    {
        $this->lastDbErrorMessage = null;

        if (!$this->validate()) {
            return false;
        }

        $key->setAttributes(
            [
                'start_date' => static::formatDate($this->startDate, 'Y-m-d'),
                'end_date' => static::formatDate($this->endDate, 'Y-m-d'),
                'change_info' => $this->changeReason,
            ],
            false
        );
        $isSaved = $key->save();
        if (!$isSaved) {
            $this->lastDbErrorMessage = $key->getLastDbErrorMessage();
            Yii::info("Failed to update key, DB error: {$key->getLastDbErrorMessage()}, model errors: " . var_export($key->getErrors(), true));
        }

        return $isSaved;
    }

    public function getLastDbErrorMessage()
    {
        return $this->lastDbErrorMessage;
    }

    public function getKeyId()
    {
        return $this->keyId;
    }
}
