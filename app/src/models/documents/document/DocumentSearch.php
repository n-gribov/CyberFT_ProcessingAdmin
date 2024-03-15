<?php

namespace app\models\documents\document;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * DocumentSearch represents the model behind the search form of `\app\models\documents\document\Document`.
 */
class DocumentSearch extends Document
{
    public $query;
    public $reg_time_range;
    public $message_codes = [];
    public $message_code_selection_id;

    /**
     * @var callable|null
     */
    public $messageCodeSelectionResolver;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['query', 'reg_time_range', 'message_code_selection_id'], 'string'],
            [['message_id', 'message_type_id', 'command_id', 'sender_id', 'receiver_id', 'status', 'send_status', 'is_error', 'send_cnt', 'send_ok', 'send_err', 'message_body', 'message_length', 'snd_terminal_id', 'rsv_terminal_id', 'message_cnt', 'curr_id', 'format_id', 'next_terminal_id'], 'integer'],
            [['query', 'message_code', 'message_name', 'sender_code', 'sender_swift_code', 'sender_name', 'receiver_code', 'receiver_swift_code', 'receiver_name', 'sender_msg_code', 'send_status_name', 'reg_time', 'finish_time', 'message_hash', 'err_code', 'err_msg', 'snd_terminal_code', 'snd_full_swift_code', 'snd_terminal_name', 'rsv_terminal_code', 'rsv_full_swift_code', 'rsv_terminal_name', 'curr_code', 'curr_num', 'curr_name', 'format_code', 'format_name', 'final_time', 'del_time', 'del_user', 'del_msg', 'next_swift_code', 'next_terminal_name', 'next_member_name', 'reg_time_range', 'message_code_selection_id'], 'safe'],
            [['message_sum'], 'number'],
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
        $query = Document::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['reg_time' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $this->andFilterByDate($query, 'reg_time');
        $this->andFilterByDate($query, 'final_time');
        $this->andFilterByDateRange($query, 'reg_time_range', 'reg_time');

        $query->andFilterWhere(['in', 'message_code', $this->message_codes]);

        // grid filtering conditions
        $query->andFilterWhere([
            'message_id' => $this->message_id,
            'message_type_id' => $this->message_type_id,
            'command_id' => $this->command_id,
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'status' => $this->status,
            'send_status' => $this->send_status,
            'is_error' => $this->is_error,
            'finish_time' => $this->finish_time,
            'send_cnt' => $this->send_cnt,
            'send_ok' => $this->send_ok,
            'send_err' => $this->send_err,
            'message_body' => $this->message_body,
            'message_length' => $this->message_length,
            'message_sum' => $this->message_sum,
            'snd_terminal_id' => $this->snd_terminal_id,
            'rsv_terminal_id' => $this->rsv_terminal_id,
            'message_cnt' => $this->message_cnt,
            'curr_id' => $this->curr_id,
            'format_id' => $this->format_id,
            'del_time' => $this->del_time,
            'next_terminal_id' => $this->next_terminal_id,
        ]);

        $query->andFilterWhere(['ilike', 'message_code', $this->message_code])
            ->andFilterWhere(['ilike', 'message_name', $this->message_name])
            ->andFilterWhere(['ilike', 'sender_code', $this->sender_code])
            ->andFilterWhere(['ilike', 'sender_swift_code', $this->sender_swift_code])
            ->andFilterWhere(['ilike', 'sender_name', $this->sender_name])
            ->andFilterWhere(['ilike', 'receiver_code', $this->receiver_code])
            ->andFilterWhere(['ilike', 'receiver_swift_code', $this->receiver_swift_code])
            ->andFilterWhere(['ilike', 'receiver_name', $this->receiver_name])
            ->andFilterWhere(['ilike', 'sender_msg_code', $this->sender_msg_code])
            ->andFilterWhere(['ilike', 'send_status_name', $this->send_status_name])
            ->andFilterWhere(['ilike', 'message_hash', $this->message_hash])
            ->andFilterWhere(['ilike', 'err_code', $this->err_code])
            ->andFilterWhere(['ilike', 'err_msg', $this->err_msg])
            ->andFilterWhere(['ilike', 'snd_terminal_code', $this->snd_terminal_code])
            ->andFilterWhere(['ilike', 'snd_full_swift_code', $this->snd_full_swift_code])
            ->andFilterWhere(['ilike', 'snd_terminal_name', $this->snd_terminal_name])
            ->andFilterWhere(['ilike', 'rsv_terminal_code', $this->rsv_terminal_code])
            ->andFilterWhere(['ilike', 'rsv_full_swift_code', $this->rsv_full_swift_code])
            ->andFilterWhere(['ilike', 'rsv_terminal_name', $this->rsv_terminal_name])
            ->andFilterWhere(['ilike', 'curr_code', $this->curr_code])
            ->andFilterWhere(['ilike', 'curr_num', $this->curr_num])
            ->andFilterWhere(['ilike', 'curr_name', $this->curr_name])
            ->andFilterWhere(['ilike', 'format_code', $this->format_code])
            ->andFilterWhere(['ilike', 'format_name', $this->format_name])
            ->andFilterWhere(['ilike', 'del_user', $this->del_user])
            ->andFilterWhere(['ilike', 'del_msg', $this->del_msg])
            ->andFilterWhere(['ilike', 'next_swift_code', $this->next_swift_code])
            ->andFilterWhere(['ilike', 'next_terminal_name', $this->next_terminal_name])
            ->andFilterWhere(['ilike', 'next_member_name', $this->next_member_name])
            ->andFilterByQuery($this->query);

        return $dataProvider;
    }

    public function load($data, $formName = null)
    {
        if (!parent::load($data, $formName)) {
            return false;
        }
        if ($this->message_code_selection_id && $this->messageCodeSelectionResolver) {
            try {
                $this->message_codes = call_user_func(
                    $this->messageCodeSelectionResolver,
                    $this->message_code_selection_id
                );
            } catch (\Exception $exception) {
                Yii::info("Failed to load message codes, caused by: $exception");
                return false;
            }
        }
        return true;
    }

    private function andFilterByDate (Query $query, string $attribute): void
    {
        if ($date = $this->parseDate($this->$attribute)) {
            $nextDay = $date->add(\DateInterval::createFromDateString('+1day'));
            $query->andWhere(['between', $attribute, $date->format('Y-m-d'), $nextDay->format('Y-m-d')]);
        }
    }

    private function andFilterByDateRange(Query $query, string $modelAttribute, string $queryAttribute): void
    {
        $startDate = null;
        $endDate = null;
        if (preg_match('/([\d.]+).*?([\d.]+)/', $this->$modelAttribute, $matches)) {
            $startDate = $this->parseDate($matches[1]);
            $endDate = $this->parseDate($matches[2]);
            if ($endDate) {
                $endDate = $endDate->add(\DateInterval::createFromDateString('+1day'));
            }
        }
        if ($startDate && $endDate) {
            $query->andWhere(['between', $queryAttribute, $startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        }
    }

    private function parseDate($value): ?\DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat('d.m.Y', $value) ?: null;
    }
}