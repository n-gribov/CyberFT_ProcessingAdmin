<?php

namespace app\models\participants\key\forms;

use app\models\participants\key\Key;
use Yii;
use yii\base\Model;

class BlockKeyForm extends Model
{
    public $blockInfo;

    private $keyId;
    private $lastDbErrorMessage;

    public static function create(Key $key): self
    {
        $form = new self();
        $form->keyId = $key->key_id;
        return $form;
    }

    public function attributeLabels()
    {
        return [
            'blockInfo' => Yii::t('app', 'Reason'),
        ];
    }

    public function rules()
    {
        return [
            ['blockInfo', 'string', 'max' => 255],
            ['blockInfo', 'safe'],
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
                'block_info' => $this->blockInfo,
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
