<?php

namespace app\models\documents\documentType;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\documents\documentType\DocumentType;

/**
 * DocumentTypeSearch represents the model behind the search form of `app\models\documents\documentType\DocumentType`.
 */
class DocumentTypeSearch extends DocumentType
{
    /**
     * {@inheritdoc}
     */
    const STATUS_ACTIVE = 1;

    public function rules()
    {
        return [
            [['type_id', 'status', 'need_permission', 'broadcast', 'group_id', 'system_id', 'system_message'], 'integer'],
            [['message_code', 'message_name', 'info', 'i_date', 'i_user', 'u_date', 'u_user', 'd_date', 'd_user', 'group_name', 'system_name'], 'safe'],
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
        $query = DocumentType::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['message_code' => SORT_ASC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'type_id' => $this->type_id,
            'status' => self::STATUS_ACTIVE,
            'need_permission' => $this->need_permission,
            'broadcast' => $this->broadcast,
            'i_date' => $this->i_date,
            'u_date' => $this->u_date,
            'd_date' => $this->d_date,
            'group_id' => $this->group_id,
            'system_id' => $this->system_id,
            'system_message' => $this->system_message,
        ]);

        $query->andFilterWhere(['ilike', 'message_code', $this->message_code])
            ->andFilterWhere(['ilike', 'message_name', $this->message_name])
            ->andFilterWhere(['ilike', 'info', $this->info])
            ->andFilterWhere(['ilike', 'i_user', $this->i_user])
            ->andFilterWhere(['ilike', 'u_user', $this->u_user])
            ->andFilterWhere(['ilike', 'd_user', $this->d_user])
            ->andFilterWhere(['ilike', 'group_name', $this->group_name])
            ->andFilterWhere(['ilike', 'system_name', $this->system_name]);

        return $dataProvider;
    }
}
