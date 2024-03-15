<?php

namespace app\models\participants\processing;

use app\models\participants\member\Attribute;
use app\models\participants\member\Member;
use app\models\participants\member\MemberAttribute;
use Yii;

/**
 * @property MemberAttribute|null $apiUrlAttribute
 * @property string|null $api_url
 */
class Processing extends Member
{
    private const API_URL_ATTRIBUTE_NAME = 'processing_api_url';
    private $apiUrl;

    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                ['is_primary', 'required'],
                ['api_url', 'string'],
                ['api_url', 'safe'],
            ]
        );
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE][] = 'api_url';
        $scenarios[self::SCENARIO_UPDATE][] = 'api_url';
        return $scenarios;
    }

    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'api_url' => Yii::t('app/participant', 'API address')
            ],
        );
    }

    /**
     * {@inheritdoc}
     * @return ProcessingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProcessingQuery(get_called_class());
    }

    public function getApi_url()
    {
        if ($this->apiUrl === null) {
            $attribute = $this->apiUrlAttribute;
            $this->apiUrl = $attribute ? $this->apiUrlAttribute->attr_value : '';
        }
        return $this->apiUrl;
    }

    public function setApi_url($value)
    {
        $this->apiUrl = $value;
    }

    public function getApiUrlAttribute()
    {
        return $this
            ->hasOne(MemberAttribute::class, ['member_id' => 'member_id'])
            ->where(['attr_name' => self::API_URL_ATTRIBUTE_NAME]);
    }

    protected function executeInsert()
    {
        $query = '
            select
                piIsErrorOut as "hasError",
                pcErrCodeOut as "errorCode",
                pcErrMsgOut as "errorMessage",
                piMemberOut as "id"
            from cyberft.p_member_api_create_processing(
                :pcMemberCode,
                :pcSwiftCode,
                :pcMemberName,
                :pcRegistrInfo,
                :piLang,
                :piIsPrimary,
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
                    ':piIsPrimary'   => $this->is_primary,
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

        $insertResult = $this->processInsertQueryResult($result);
        if ($insertResult === false) {
            return false;
        }
        $this->saveApiUrl();
        return $insertResult;
    }

    protected function executeUpdate()
    {
        $query = '
            select
                piIsErrorOut as "hasError",
                pcErrCodeOut as "errorCode",
                pcErrMsgOut as "errorMessage"
            from cyberft.p_member_api_update_processing(
                :piMember,
                :pcMemberCode,
                :pcSwiftCode,
                :pcMemberName,
                :pcRegistrInfo,
                :piLang,
                :piIsPrimary,
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
                    ':piIsPrimary'   => $this->is_primary,
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

        $this->saveApiUrl();

        return $this->updateAutoModifyAttribute();
    }

    private function saveApiUrl()
    {
        $memberAttribute = $this->apiUrlAttribute;
        $oldValue = $memberAttribute ? $memberAttribute->attr_value : null;
        if ($oldValue == $this->api_url) {
            return;
        }
        $urlAttribute = $this->findOrCreateAttribute(self::API_URL_ATTRIBUTE_NAME);
        if ($memberAttribute) {
            $memberAttribute->delete();
        }
        $memberAttribute = new MemberAttribute([
            'member_id'  => $this->member_id,
            'attr_id'    => $urlAttribute->attr_id,
            'attr_value' => $this->api_url,
        ]);
        $memberAttribute->save();
    }

    private function findOrCreateAttribute(string $name): Attribute
    {
        $attribute = Attribute::findOne(['attr_name' => $name]);
        if (!$attribute) {
            $attribute = new Attribute(['attr_name' => $name]);
            $attribute->save();
        }
        return $attribute;
    }
}
