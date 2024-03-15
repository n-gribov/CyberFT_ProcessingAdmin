<?php

namespace app\models\participants\operator;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\participants\operator\Operator;

/**
 * OperatorSearch represents the model behind the search form of `\app\models\participants\operator\Operator`.
 */
class OperatorSearch extends Operator
{
    public $query;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['query', 'string'],
            [['operator_id', 'member_id', 'member_type', 'status', 'blocked', 'role_id', 'full_privs', 'terminal_id'], 'integer'],
            [['query', 'member_code', 'swift_code', 'member_name', 'operator_name', 'block_info', 'role_name', 'terminal_code', 'full_swift_code', 'terminal_name', 'operator_position', 'phone', 'email', 'i_date', 'i_user', 'u_date', 'u_user', 'external_code'], 'safe'],
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
        $query = Operator::find()->notDeleted();

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
            'operator_id' => $this->operator_id,
            'member_id' => $this->member_id,
            'member_type' => $this->member_type,
            'status' => $this->status,
            'blocked' => $this->blocked,
            'role_id' => $this->role_id,
            'full_privs' => $this->full_privs,
            'terminal_id' => $this->terminal_id,
            'i_date' => $this->i_date,
            'u_date' => $this->u_date,
        ]);

        $query->andFilterWhere(['ilike', 'member_code', $this->member_code])
            ->andFilterWhere(['ilike', 'swift_code', $this->swift_code])
            ->andFilterWhere(['ilike', 'member_name', $this->member_name])
            ->andFilterWhere(['ilike', 'operator_name', $this->operator_name])
            ->andFilterWhere(['ilike', 'block_info', $this->block_info])
            ->andFilterWhere(['ilike', 'role_name', $this->role_name])
            ->andFilterWhere(['ilike', 'terminal_code', $this->terminal_code])
            ->andFilterWhere(['ilike', 'full_swift_code', $this->full_swift_code])
            ->andFilterWhere(['ilike', 'terminal_name', $this->terminal_name])
            ->andFilterWhere(['ilike', 'operator_position', $this->operator_position])
            ->andFilterWhere(['ilike', 'phone', $this->phone])
            ->andFilterWhere(['ilike', 'email', $this->email])
            ->andFilterWhere(['ilike', 'i_user', $this->i_user])
            ->andFilterWhere(['ilike', 'u_user', $this->u_user])
            ->andFilterWhere(['ilike', 'external_code', $this->external_code])
            ->andFilterByQuery($this->query);

        return $dataProvider;
    }
}
