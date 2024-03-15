<?php

namespace app\models\participants\member;

use Yii;
use yii\base\Model;

class BlockMemberForm extends Model
{
    public $reason;

    private $member;
    private $lastDbErrorMessage = null;

    public static function create(Member $member): self
    {
        $form = new self();
        $form->member = $member;
        return $form;
    }

    public function attributeLabels()
    {
        return [
            'reason' => Yii::t('app', 'Reason'),
        ];
    }

    public function rules()
    {
        return [
            ['reason', 'string', 'max' => 255],
            ['reason', 'safe'],
        ];
    }

    public function save(): bool
    {
        $this->lastDbErrorMessage = null;
        $isSaved = $this->member->setBlockStatus(true, $this->reason);
        if (!$isSaved) {
            $this->lastDbErrorMessage = $this->member->getLastDbErrorMessage();
        }
        return $isSaved;
    }

    public function getLastDbErrorMessage()
    {
        return $this->lastDbErrorMessage;
    }

    public function getMemberId()
    {
        return $this->member->member_id;
    }
}
