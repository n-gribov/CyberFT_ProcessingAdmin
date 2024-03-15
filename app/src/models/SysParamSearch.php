<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SysParam;

/**
 * SysParamSearch represents the model behind the search form of `app\models\SysParam`.
 */
class SysParamSearch extends SysParam
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['par_code', 'par_value', 'par_descr', 'par_type_name', 'par_type_descr'], 'safe'],
            [['is_visible', 'is_modifiable', 'par_type'], 'integer'],
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
        $query = SysParam::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['par_code' => SORT_ASC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'is_visible' => 1,    
            //'is_visible' => $this->is_visible,
            'is_modifiable' => $this->is_modifiable,
            'par_type' => $this->par_type,
        ]);

        $query->andFilterWhere(['ilike', 'par_code', $this->par_code])
            ->andFilterWhere(['ilike', 'par_value', $this->par_value])
            ->andFilterWhere(['ilike', 'par_descr', $this->par_descr])
            ->andFilterWhere(['ilike', 'par_type_name', $this->par_type_name])
            ->andFilterWhere(['ilike', 'par_type_descr', $this->par_type_descr]);

        return $dataProvider;
    }
}
