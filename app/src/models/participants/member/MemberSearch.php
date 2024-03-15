<?php

namespace app\models\participants\member;

use app\models\documents\document\Document;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

trait MemberSearch
{
    public $query;
    public $partnerMemberSwiftCode;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['query', 'string'],
            [['member_id', 'status', 'blocked', 'lang_num', 'member_type', 'parent_id', 'is_primary', 'is_bank', 'auto_modify'], 'integer'],
            [['query', 'member_code', 'swift_code', 'member_name', 'registr_info', 'status_name', 'block_info', 'i_date', 'i_user', 'u_date', 'u_user', 'd_date', 'd_user', 'full_swift_code', 'lang_name', 'member_type_name', 'proc_swift_code', 'proc_name', 'eng_name', 'cntr_code2', 'cntr_name', 'city_name', 'valid_from', 'valid_to', 'website', 'member_phone', 'a_date', 'a_user', 'partnerMemberSwiftCode'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = static::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'member_id' => $this->member_id,
            'blocked' => $this->blocked,
            'u_date' => $this->u_date,
            'd_date' => $this->d_date,
            'lang_num' => $this->lang_num,
            'member_type' => $this->member_type,
            'parent_id' => $this->parent_id,
            'is_primary' => $this->is_primary,
            'is_bank' => $this->is_bank,
            'valid_from' => $this->valid_from,
            'valid_to' => $this->valid_to,
            'auto_modify' => $this->auto_modify,
            'a_date' => $this->a_date,
        ]);

        if ($date = \DateTime::createFromFormat('d.m.Y', $this->i_date)) {
            $query->andWhere("date_trunc('day', i_date) = '" . $date->format('Y-m-d') . "'");
        }

        $query->andFilterWhere(['ilike', 'member_code', $this->member_code])
            ->andFilterWhere(['ilike', 'swift_code', $this->swift_code])
            ->andFilterWhere(['ilike', 'member_name', $this->member_name])
            ->andFilterWhere(['ilike', 'registr_info', $this->registr_info])
            ->andFilterWhere(['ilike', 'block_info', $this->block_info])
            ->andFilterWhere(['ilike', 'i_user', $this->i_user])
            ->andFilterWhere(['ilike', 'u_user', $this->u_user])
            ->andFilterWhere(['ilike', 'd_user', $this->d_user])
            ->andFilterWhere(['ilike', 'full_swift_code', $this->full_swift_code])
            ->andFilterWhere(['ilike', 'lang_name', $this->lang_name])
            ->andFilterWhere(['ilike', 'member_type_name', $this->member_type_name])
            ->andFilterWhere(['ilike', 'proc_swift_code', $this->proc_swift_code])
            ->andFilterWhere(['ilike', 'proc_name', $this->proc_name])
            ->andFilterWhere(['ilike', 'eng_name', $this->eng_name])
            ->andFilterWhere(['ilike', 'cntr_code2', $this->cntr_code2])
            ->andFilterWhere(['ilike', 'cntr_name', $this->cntr_name])
            ->andFilterWhere(['ilike', 'city_name', $this->city_name])
            ->andFilterWhere(['ilike', 'website', $this->website])
            ->andFilterWhere(['ilike', 'member_phone', $this->member_phone])
            ->andFilterWhere(['ilike', 'a_user', $this->a_user])
            ->andFilterByQuery($this->query);

        $this->applyStatusFilter($query);
        $this->applyPartnerMemberFilter($query);

        return $dataProvider;
    }

    private function applyStatusFilter(Query $query)
    {
        if ($this->status === null || $this->status === '') {
            return;
        }

        if ($this->status == MemberStatus::STATUS_NOT_DELETED) {
            $query->andWhere(['!=', 'status', MemberStatus::STATUS_DELETED]);
        } else {
            $query->andWhere(['status' => $this->status]);
        }
    }

    private function applyPartnerMemberFilter(Query $query): void
    {
        if (!$this->partnerMemberSwiftCode) {
            return;
        }

        $senderSwiftCodes = Document::find()
            ->select(['sender_swift_code'])
            ->distinct()
            ->successfullyDelivered()
            ->andWhere('sender_swift_code is not null')
            ->andWhere(['receiver_swift_code' => $this->partnerMemberSwiftCode])
            ->column();
        $receiverSwiftCodes = Document::find()
            ->select(['receiver_swift_code'])
            ->distinct()
            ->successfullyDelivered()
            ->andWhere('receiver_swift_code is not null')
            ->andWhere(['sender_swift_code' => $this->partnerMemberSwiftCode])
            ->column();

        $query->andWhere(['swift_code' => array_merge($receiverSwiftCodes, $senderSwiftCodes)]);
    }
}
