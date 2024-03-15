<?php

namespace app\models\participants\terminal;

use Yii;
use yii\base\Model;

class BlockTerminalForm extends Model
{
    public $reason;

    private $terminal;
    private $lastDbErrorMessage = null;

    public static function create(Terminal $terminal): self
    {
        $form = new self();
        $form->terminal = $terminal;
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
        $isSaved = $this->terminal->setBlockStatus(true, $this->reason);
        if (!$isSaved) {
            $this->lastDbErrorMessage = $this->terminal->getLastDbErrorMessage();
        }
        return $isSaved;
    }

    public function getLastDbErrorMessage()
    {
        return $this->lastDbErrorMessage;
    }

    public function getTerminalId()
    {
        return $this->terminal->terminal_id;
    }
}
