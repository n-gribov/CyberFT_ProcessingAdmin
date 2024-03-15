<?php

namespace app\models\participants\key;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\participants\key\Key;
use app\models\participants\operator\Operator;
use yii\db\Query;

/**
 * KeySearch represents the model behind the search form of `\app\models\participants\key\Key`.
 */
class KeySearch extends Key
{
    public $query;
    public $ownerRole;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['query', 'ownerRole'], 'string'],
            [['key_id', 'owner_type', 'owner_id', 'key_type', 'crypto_type', 'cert_center', 'status'], 'integer'],
            [['query', 'owner_type_name', 'owner_code', 'swift_code', 'owner_name', 'key_type_name', 'key_code', 'status_name', 'block_info', 'start_date', 'end_date', 'i_date', 'i_user', 'd_date', 'd_user', 'b_date', 'b_user', 'key_body', 'u_date', 'u_user', 'change_info'], 'safe'],
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
        $role = isset($params['KeySearch']['ownerRole']) ? $params['KeySearch']['ownerRole'] : null;
        $status = isset($params['KeySearch']['status']) ? $params['KeySearch']['status'] : null;

        if ($role == null) {
            $query = Key::find()->notDeleted();
        } elseif ($role != null) {
            $query = Key::find()->joinWith(['role r'])
                                ->where(['r.role_id' => (int) $role])
                                ->andWhere(Key::tableName().'.owner_id'.' != 0');
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['status' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'key_id' => $this->key_id,
            'owner_type' => $this->owner_type,
            'owner_id' => $this->owner_id,
            'key_type' => $this->key_type,
            'crypto_type' => $this->crypto_type,
            'cert_center' => $this->cert_center,
            'v_keys.status' => $this->status == null ? null : (int) $this->status,
            'start_date' => $this->start_date == null ? null : Self::dateFormat($this->start_date),
            'end_date' => $this->end_date == null ? null : Self::dateFormat($this->end_date),
            'd_date' => $this->d_date,
            'b_date' => $this->b_date,
            'u_date' => $this->u_date,
        ]);

        if ($this->i_date != null) {
            $i_date_first = Self::dateFormat($this->i_date).' 00:00:00';
            $i_date_last = Self::dateFormat($this->i_date).' 23:59:59';
            $query->andFilterWhere(['between', Key::tableName().'.i_date', $i_date_first, $i_date_last]);
        }

        $query->andFilterWhere(['ilike', 'owner_type_name', $this->owner_type_name])
            ->andFilterWhere(['ilike', 'owner_code', $this->owner_code])
            ->andFilterWhere(['ilike', 'swift_code', $this->swift_code])
            ->andFilterWhere(['ilike', 'owner_name', $this->owner_name])
            ->andFilterWhere(['ilike', 'key_type_name', $this->key_type_name])
            ->andFilterWhere(['ilike', 'key_code', $this->key_code])
            ->andFilterWhere(['ilike', 'block_info', $this->block_info])
            ->andFilterWhere(['ilike', 'i_user', $this->i_user])
            ->andFilterWhere(['ilike', 'd_user', $this->d_user])
            ->andFilterWhere(['ilike', 'b_user', $this->b_user])
            ->andFilterWhere(['ilike', 'key_body', $this->key_body])
            ->andFilterWhere(['ilike', 'u_user', $this->u_user])
            ->andFilterWhere(['ilike', 'change_info', $this->change_info])
            ->andFilterByQuery($this->query);

        return $dataProvider;
    }

    public static function getOwnerRoles()
    {
        return [
            static::ROLE_SIGNATORY => Yii::t('app/key','Signatory'),
            static::ROLE_CONTROLLER => Yii::t('app/key','Controller'),
        ];
    }

    public static function getKeysStatus()
    {
        return [
            static::STATUS_AWAITING_ACTIVATION => Yii::t('app/key', 'Awaiting activation'),
            static::STATUS_ACTIVE => Yii::t('app/key', 'Active'),
            static::STATUS_BLOCKED => Yii::t('app/key', 'Blocked'),
        ];
    }

    public static function dateFormat($date)
    {
        if ($datetime = \DateTime::createFromFormat('d.m.Y', $date)) {
            return $datetime->format('Y-m-d');
        }
    }
}
