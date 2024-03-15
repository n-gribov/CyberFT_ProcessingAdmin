<?php

namespace app\models\participants\member;

use app\models\ActiveRecord;
use Yii;

/**
 * This is the model class for table "cyberft.v_members".
 *
 * @property int $member_id
 * @property string $member_code
 * @property string $swift_code
 * @property string $member_name
 * @property string $registr_info
 * @property int $status
 * @property string $status_name
 * @property int $blocked
 * @property string $block_info
 * @property string $i_date
 * @property string $i_user
 * @property string $u_date
 * @property string $u_user
 * @property string $d_date
 * @property string $d_user
 * @property string $full_swift_code
 * @property int $lang_num
 * @property string $lang_name
 * @property int $member_type
 * @property string $member_type_name
 * @property int $parent_id
 * @property string $proc_swift_code
 * @property string $proc_name
 * @property int $is_primary
 * @property string $eng_name
 * @property int $is_bank
 * @property string $cntr_code2
 * @property string $cntr_name
 * @property string $city_name
 * @property string $valid_from
 * @property string $valid_to
 * @property string $website
 * @property string $member_phone
 * @property int $auto_modify
 * @property string $a_date
 * @property string $a_user
 */
abstract class Member extends ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public static function tableName()
    {
        return 'cyberft.v_members';
    }

    public static function primaryKey()
    {
        return ['member_id'];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $assignableAttributes = [
            'member_code', 'swift_code', 'member_name', 'registr_info', 'lang_num', 'parent_id', 'is_primary',
            'eng_name', 'is_bank', 'cntr_code2', 'city_name', 'website', 'member_phone', 'auto_modify',
        ];
        $scenarios[self::SCENARIO_CREATE] = $assignableAttributes;
        $scenarios[self::SCENARIO_UPDATE] = $assignableAttributes;
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['member_id', 'status', 'blocked', 'lang_num', 'member_type', 'parent_id', 'is_primary', 'auto_modify'], 'default', 'value' => null],
            [['member_id', 'status', 'blocked', 'lang_num', 'member_type', 'parent_id', 'is_primary', 'is_bank', 'auto_modify'], 'integer'],
            [['registr_info', 'member_type_name', 'website'], 'string'],
            [['i_date', 'u_date', 'd_date', 'valid_from', 'valid_to', 'a_date'], 'safe'],
            [['member_code', 'swift_code', 'i_user', 'u_user', 'd_user', 'full_swift_code', 'proc_swift_code', 'a_user'], 'string', 'max' => 80],
            [['member_name', 'block_info', 'proc_name', 'eng_name', 'cntr_name', 'city_name', 'member_phone'], 'string', 'max' => 255],
            [['status_name', 'cntr_code2'], 'string', 'max' => 20],
            [['lang_name'], 'string', 'max' => 125],
            ['member_type', 'default', 'value' => 2],
            [['swift_code', 'member_name', 'is_bank'], 'required'],
            ['swift_code', 'match', 'pattern' => '/^[A-Z\d]{6,7}[A-Z\d@]?[A-Z\d]{3}$/']
        ];
    }

    public function attributeLabels()
    {
        return [
            'member_id' => Yii::t('app/model', 'ID'),
            'member_code' => Yii::t('app/participant', 'Code (legacy)'),
            'swift_code' => Yii::t('app/model', 'Code'),
            'member_name' => Yii::t('app/model', 'Name'),
            'registr_info' => Yii::t('app/participant', 'Registration info'),
            'status' => Yii::t('app/model', 'Status'),
            'status_name' => Yii::t('app/model', 'Status'),
            'blocked' => Yii::t('app/model', 'Blocked'),
            'block_info' => Yii::t('app/model', 'Blocking reason'),
            'i_date' => Yii::t('app/model', 'Created at'),
            'i_user' => Yii::t('app/model', 'Created by'),
            'u_date' => Yii::t('app/model', 'Updated at'),
            'u_user' => Yii::t('app/model', 'Updated by'),
            'd_date' => Yii::t('app/model', 'Deleted at'),
            'd_user' => Yii::t('app/model', 'Deleted by'),
            'full_swift_code' => Yii::t('app/participant', 'Full Swift code'),
            'lang_num' => Yii::t('app', 'Language'),
            'lang_name' => Yii::t('app', 'Language'),
            'member_type' => Yii::t('app/participant', 'Record type'),
            'member_type_name' => Yii::t('app/participant', 'Record type'),
            'parent_id' => Yii::t('app/participant', 'Processing'),
            'proc_swift_code' => Yii::t('app/participant', 'Processing code'),
            'proc_name' => Yii::t('app/participant', 'Processing name'),
            'is_primary' => Yii::t('app/participant', 'Is primary processing'),
            'eng_name' => Yii::t('app/participant', 'International name'),
            'is_bank' => Yii::t('app/participant', 'Is bank'),
            'cntr_code2' => Yii::t('app/participant', 'Country code'),
            'cntr_name' => Yii::t('app/participant', 'Country'),
            'city_name' => Yii::t('app/participant', 'City'),
            'valid_from' => Yii::t('app/participant', 'Valid from'),
            'valid_to' => Yii::t('app/participant', 'Valid to'),
            'website' => Yii::t('app/participant', 'Website'),
            'member_phone' => Yii::t('app/participant', 'Phone number'),
            'auto_modify' => Yii::t('app/participant', 'Auto update'),
            'a_date' => Yii::t('app/participant', 'Auto update edited at'),
            'a_user' => Yii::t('app/participant', 'Auto update edited by'),
            'tariff_plan' => Yii::t('app/participant', 'Tariff plan')
        ];
    }

    public function setBlockStatus(bool $isBlock, $reason): bool
    {
        $query = '
            select
                piiserrorout as "hasError",
                pcerrcodeout as "errorCode",
                pcerrmsgout as "errorMessage"
            from cyberft.p_member_api_block_member(
                :piMember,
                :piBlock,
                :pcBlockInfo
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    'piMember' => $this->getPrimaryKey(),
                    'piBlock' => $isBlock ? 1 : 0,
                    'pcBlockInfo' => $reason,
                ]
            )
            ->queryOne();

        return $this->processRecordChangeQueryResult($result, $isBlock ? 'block' : 'unblock') !== false;
    }

    protected function updateAutoModifyAttribute()
    {
        if (!array_key_exists('auto_modify', $this->dirtyAttributes)) {
            return 1;
        }

        $query = '
            select
                piIsErrorOut as "hasError",
                pcErrCodeOut as "errorCode",
                pcErrMsgOut as "errorMessage"
            from cyberft.p_member_api_set_auto_modif(
                :piMember,
                :piAutoModify
            )
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [
                    ':piMember'     => $this->member_id,
                    ':piAutoModify' => $this->auto_modify,
                ]
            )
            ->queryOne();

        return $this->processUpdateQueryResult($result);
    }

    protected function executeDelete()
    {
        $query = '
            select
                piiserrorout as "hasError",
                pcerrcodeout as "errorCode",
                pcerrmsgout as "errorMessage"
            from cyberft.p_member_api_delete_member(:piMember, 1)
        ';

        $result = Yii::$app->db
            ->createCommand(
                $query,
                [':piMember' => $this->member_id]
            )
            ->queryOne();

        return $this->processDeleteQueryResult($result);
    }
}
