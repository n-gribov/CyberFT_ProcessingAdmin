<?php

namespace app\models\participants\terminal;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\participants\terminal\Terminal;

/**
 * TerminalSearch represents the model behind the search form of `\app\models\participants\terminal\Terminal`.
 */
class TerminalSearch extends Terminal
{
    public $query;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['query', 'string'],
            [['terminal_id', 'member_id', 'member_type', 'status', 'work_mode', 'blocked', 'fragment_ready', 'fragment_size', 'queue_length'], 'integer'],
            [['query', 'member_swift_code', 'member_name', 'terminal_code', 'full_swift_code', 'terminal_name', 'status_name', 'work_mode_name', 'block_info', 'i_date', 'i_user', 'u_date', 'u_user', 'd_date', 'd_user', 'login_param', 'login_addr'], 'safe'],
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
        $query = Terminal::find()->notDeleted();

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
            'terminal_id' => $this->terminal_id,
            'member_id' => $this->member_id,
            'member_type' => $this->member_type,
            'status' => $this->status,
            'work_mode' => $this->work_mode,
            'blocked' => $this->blocked,
            'i_date' => $this->i_date,
            'u_date' => $this->u_date,
            'd_date' => $this->d_date,
            'fragment_ready' => $this->fragment_ready,
            'fragment_size' => $this->fragment_size,
            'queue_length' => $this->queue_length,
        ]);

        $query->andFilterWhere(['ilike', 'member_swift_code', $this->member_swift_code])
            ->andFilterWhere(['ilike', 'member_name', $this->member_name])
            ->andFilterWhere(['ilike', 'terminal_code', $this->terminal_code])
            ->andFilterWhere(['ilike', 'full_swift_code', $this->full_swift_code])
            ->andFilterWhere(['ilike', 'terminal_name', $this->terminal_name])
            ->andFilterWhere(['ilike', 'status_name', $this->status_name])
            ->andFilterWhere(['ilike', 'work_mode_name', $this->work_mode_name])
            ->andFilterWhere(['ilike', 'block_info', $this->block_info])
            ->andFilterWhere(['ilike', 'i_user', $this->i_user])
            ->andFilterWhere(['ilike', 'u_user', $this->u_user])
            ->andFilterWhere(['ilike', 'd_user', $this->d_user])
            ->andFilterWhere(['ilike', 'login_param', $this->login_param])
            ->andFilterWhere(['ilike', 'login_addr', $this->login_addr])
            ->andFilterByQuery($this->query);;

        return $dataProvider;
    }
}
