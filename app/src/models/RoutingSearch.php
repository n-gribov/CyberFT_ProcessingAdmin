<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Routing;

/**
 * RoutingSearch represents the model behind the search form of `app\models\Routing`.
 */
class RoutingSearch extends Routing
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['route_id', 'dst_node', 'src_node'], 'integer'],
            [['dst_swift_code', 'dst_member_name', 'src_swift_code', 'src_member_name'], 'safe'],
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
        $query = Routing::find();

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
            'route_id' => $this->route_id,
            'dst_node' => $this->dst_node,
            'src_node' => $this->src_node,
        ]);

        $query->andFilterWhere(['ilike', 'dst_swift_code', $this->dst_swift_code])
            ->andFilterWhere(['ilike', 'dst_member_name', $this->dst_member_name])
            ->andFilterWhere(['ilike', 'src_swift_code', $this->src_swift_code])
            ->andFilterWhere(['ilike', 'src_member_name', $this->src_member_name]);

        return $dataProvider;
    }
}
