<?php

namespace app\models\participants\participant;

use app\models\participants\member\Member;
use app\models\participants\processing\Processing;
use Yii;

/**
 * @property bool $isLocal
 * @property Processing $processing
 */
class Participant extends Member
{
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [['parent_id', 'required']]
        );
    }

    /**
     * {@inheritdoc}
     * @return ParticipantQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ParticipantQuery(get_called_class());
    }

    public function getProcessing()
    {
        return $this->hasOne(Processing::class, ['member_id' => 'parent_id']);
    }

    public function getIsLocal(): bool
    {
        return $this->processing !== null && $this->processing->is_primary;
    }

    protected function executeInsert()
    {
        $query = '
            select
                piIsErrorOut as "hasError",
                pcErrCodeOut as "errorCode",
                pcErrMsgOut as "errorMessage",
                piMemberOut as "id"
            from cyberft.p_member_api_create_member(
                :pcMemberCode,
                :pcSwiftCode,
                :pcMemberName,
                :pcRegistrInfo,
                :piLang,
                :piParent,
                :pcEngName,
                :piIsBank,
                :pcCntrCode2,
                :pcCityName,
                :pcWebSite,
                :pcMemberPhone,
                :piAutoModify
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':pcMemberCode'  => $this->member_code,
                    ':pcSwiftCode'   => $this->swift_code,
                    ':pcMemberName'  => $this->member_name,
                    ':pcRegistrInfo' => $this->registr_info,
                    ':piLang'        => $this->lang_num,
                    ':piParent'      => $this->parent_id,
                    ':pcEngName'     => $this->eng_name,
                    ':piIsBank'      => $this->is_bank,
                    ':pcCntrCode2'   => $this->cntr_code2,
                    ':pcCityName'    => $this->city_name,
                    ':pcWebSite'     => $this->website,
                    ':pcMemberPhone' => $this->member_phone,
                    ':piAutoModify'  => $this->auto_modify,
                ]
            )
            ->queryOne();

        return $this->processInsertQueryResult($result);
    }

    protected function executeUpdate()
    {
        $query = '
            select
                piIsErrorOut as "hasError",
                pcErrCodeOut as "errorCode",
                pcErrMsgOut as "errorMessage"
            from cyberft.p_member_api_update_member(
                :piMember,
                :pcMemberCode,
                :pcSwiftCode,
                :pcMemberName,
                :pcRegistrInfo,
                :piLang,
                :piParent,
                :pcEngName,
                :piIsBank,
                :pcCntrCode2,
                :pcCityName,
                :pcWebSite,
                :pcMemberPhone
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':piMember'      => $this->member_id,
                    ':pcMemberCode'  => $this->member_code,
                    ':pcSwiftCode'   => $this->swift_code,
                    ':pcMemberName'  => $this->member_name,
                    ':pcRegistrInfo' => $this->registr_info,
                    ':piLang'        => $this->lang_num,
                    ':piParent'      => $this->parent_id,
                    ':pcEngName'     => $this->eng_name,
                    ':piIsBank'      => $this->is_bank,
                    ':pcCntrCode2'   => $this->cntr_code2,
                    ':pcCityName'    => $this->city_name,
                    ':pcWebSite'     => $this->website,
                    ':pcMemberPhone' => $this->member_phone,
                ]
            )
            ->queryOne();

        $updateResult = $this->processUpdateQueryResult($result);
        if ($updateResult === false) {
            return false;
        }

        return $this->updateAutoModifyAttribute();
    }
}
