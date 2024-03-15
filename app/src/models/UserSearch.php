<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

class UserSearch extends User
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'status', 'send_mail', 'account_status'], 'integer'],
            [['user_code', 'user_name', 'comm', 'i_date', 'd_date', 'i_user', 'd_user', 'l_login', 'll_login', 'e_mail'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = User::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'user_id' => $this->user_id,
            'status' => $this->status,
            'i_date' => $this->i_date,
            'd_date' => $this->d_date,
            'l_login' => $this->l_login,
            'll_login' => $this->ll_login,
            'send_mail' => $this->send_mail,
            'account_status' => $this->account_status,
        ]);

        $query->andFilterWhere(['ilike', 'user_code', $this->user_code])
            ->andFilterWhere(['ilike', 'user_name', $this->user_name])
            ->andFilterWhere(['ilike', 'comm', $this->comm])
            ->andFilterWhere(['ilike', 'i_user', $this->i_user])
            ->andFilterWhere(['ilike', 'd_user', $this->d_user])
            ->andFilterWhere(['ilike', 'e_mail', $this->e_mail]);

        return $dataProvider;
    }
}
