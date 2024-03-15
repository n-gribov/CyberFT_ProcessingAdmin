<?php

namespace app\models\participants\key\forms;

use app\models\participants\key\Key;
use app\models\participants\operator\Operator;
use app\models\X509Certificate;
use Yii;
use yii\base\Model;

class CreateKeyForm extends Model
{
    public $ownerId;
    public $code;
    public $startDate;
    public $endDate;
    public $keyBodyBase64;

    private $lastDbErrorMessage;

    public function attributeLabels()
    {
        return [
            'ownerId' => Yii::t('app/model', 'Owner'),
            'code' => Yii::t('app/model', 'Code'),
            'startDate' => Yii::t('app/key', 'Start date'),
            'endDate' => Yii::t('app/key', 'End date'),
            'keyBodyFile' => Yii::t('app/key', 'Key body'),
        ];
    }

    public function rules()
    {
        return [
            ['ownerId', 'integer'],
            ['code', 'string', 'max' => 80],
            ['keyBodyBase64', 'string'],
            ['startDate', 'date', 'format' => 'php:d.m.Y'],
            [['ownerId', 'code', 'startDate', 'keyBodyBase64'], 'required'],
            [['ownerId', 'code', 'startDate', 'endDate', 'keyBodyBase64'], 'safe'],
        ];
    }

    public static function create($ownerId, $keyBody): self
    {
        $form = new self(['ownerId' => $ownerId]);

        $operator = Operator::findOne($ownerId);
        if ($operator === null) {
            return $form;
        }

        $x509Certificate = new X509Certificate($keyBody);
        $fingerprint = strtoupper($x509Certificate->getFingerprint());
        $form->code = "{$operator->full_swift_code}-$fingerprint";
        $form->startDate = $x509Certificate->getValidFrom() ? $x509Certificate->getValidFrom()->format('d.m.Y') : null;
        $form->endDate = $x509Certificate->getValidTo() ? $x509Certificate->getValidTo()->format('d.m.Y') : null;
        $form->keyBodyBase64 = base64_encode($x509Certificate->getBody());

        return $form;
    }

    public function save()
    {
        $this->lastDbErrorMessage = null;

        if (!$this->validate()) {
            return false;
        }

        $formatDate = function ($dmy) {
            if (empty($dmy)) {
                return null;
            }
            return (new \DateTime($dmy))->format('Y-m-d');
        };

        $key = new Key([
            'owner_type' => Key::KEY_OWNER_TYPE_OPERATOR,
            'owner_id' => $this->ownerId,
            'key_type' => Key::KEY_TYPE_PUBLIC,
            'key_code' => $this->code,
            'key_body' => base64_decode($this->keyBodyBase64),
            'start_date' => $formatDate($this->startDate),
            'end_date' => $formatDate($this->endDate),
        ]);
        $isSaved = $key->save();
        if (!$isSaved) {
            $this->lastDbErrorMessage = $key->getLastDbErrorMessage();
            Yii::info("Failed to save new key, DB error: {$key->getLastDbErrorMessage()}, model errors: " . var_export($key->getErrors(), true));
        }

        return $isSaved;
    }

    public function getLastDbErrorMessage()
    {
        return $this->lastDbErrorMessage;
    }
}
