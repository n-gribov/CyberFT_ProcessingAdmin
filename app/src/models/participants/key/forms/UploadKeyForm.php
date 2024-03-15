<?php

namespace app\models\participants\key\forms;

use app\models\X509Certificate;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class UploadKeyForm extends Model
{
    public $ownerId;
    public $keyBodyFile;

    private $keyBody;

    public function attributeLabels()
    {
        return [
            'ownerId' => Yii::t('app/model', 'Owner'),
            'keyBodyFile' => Yii::t('app/key', 'Certificate'),
        ];
    }

    public function rules()
    {
        return [
            ['ownerId', 'integer'],
            ['keyBodyFile', 'file'],
            [['ownerId', 'keyBodyFile'], 'required'],
            ['keyBodyFile', 'validateKeyBodyFile'],
        ];
    }

    public function load($data, $formName = null)
    {
        if (parent::load($data, $formName)) {
            return $this->loadKeyBody();
        }
        return false;
    }

    private function loadKeyBody(): bool
    {
        $this->keyBody = null;

        $this->keyBodyFile = UploadedFile::getInstance($this, 'keyBodyFile');
        if ($this->keyBodyFile === null) {
            Yii::error('Failed to load uploaded file');
            return false;
        }

        $keyBody = file_get_contents($this->keyBodyFile->tempName);
        if ($keyBody === false) {
            Yii::error('Failed to read uploaded file');
            return false;
        }

        $this->keyBody = $keyBody;

        return true;
    }

    public function getKeyBody()
    {
        return $this->keyBody;
    }

    public function validateKeyBodyFile($attribute, $params = [])
    {
        try {
            new X509Certificate($this->keyBody);
        } catch (\Throwable $exception) {
            $this->addError($attribute, Yii::t('app/key', 'Invalid public key format'));
        }
    }
}
