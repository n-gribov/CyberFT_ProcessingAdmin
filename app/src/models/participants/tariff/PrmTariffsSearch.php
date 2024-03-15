<?php

namespace app\models\participants\tariff;

use app\models\participants\tariff\PrmTariffs;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\db\ActiveQuery;

/**
 * PrmTariffsSearch represents the model behind the search form of `app\models\participants\tariff\PrmTariffs`.
 */
class PrmTariffsSearch extends PrmTariffs
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_RESERVED = 2;
    const STATUS_NOT_DELETED = 3;

    public $query;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['trf_prm_id', 'trf_id', 'msg_type_id', 'is_register', 'amnd_state', 'prnt_id'], 'integer'],
            [['amnd_date', 'author'], 'safe'],
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
        //$this->load($params) не хочет работать
        $this->trf_id = $params['trf_id'];
        
        $query = PrmTariffs::find()->alias('p');  
        
        $this->applyStatusFilter($query);
        $this->applyExtFilters($query);      

        $query->andWhere(['trf_id' => $this->trf_id]);

        $arrayCount = $this->getArrayCount($query);
        
        $query->orderBy('p.msg_type_id desc, p.is_register, pe.trf_prm_id, pe.docs_cnt_from');
        
        //основной dataProvider
        $dataProvider = new SqlDataProvider([
            'sql' => $query->createCommand()->getRawSql(),           
        ]);        
                
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        return compact('dataProvider', 'arrayCount');
    }
    
    public function searchAmounts($params)
    {
        $this->msg_type_id = $params['msg_type_id'];
        $this->trf_id = $params['trf_id'];
        $this->is_register = $params['is_register'];
        
        $query = PrmTariffs::find()->alias('p');
        
        $this->applyAmountExtFilters($query);    
        
        $query->andWhere(['msg_type_id' => $this->msg_type_id]);
        $query->andWhere(['trf_id' => $this->trf_id]);
        $query->andWhere(['is_register' => $this->is_register]);
        
        $dataProvider = new SqlDataProvider([
            'sql' => $query->createCommand()->getRawSql(),           
        ]);
                
        
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        return compact('dataProvider');
    }
    
    public function getArrayCount($query)
    {
        $queryCount = clone $query;
        
        $this->countDuplicates($queryCount);
        
        //dataProvider для rowspan'ов
        $dataProviderCount = new SqlDataProvider([
            'sql' => $queryCount->createCommand()->getRawSql(),
        ]);        
        return $dataProviderCount->getModels();
    }
    
    public function applyExtFilters($query)
    {      
        $query->joinWith(['prmTariffsExt pe'])
              ->joinWith(['documentType dt'])
              ->select([
                  'p.trf_id',
                  'p.is_register',
                  'p.msg_type_id',
                  'pe.trf_prm_id',
                  'pe.trf_prm_ext_id',
                  'pe.docs_cnt_from',
                  'pe.docs_cnt_to',
                  'pe.price_sent_doc',
                  'pe.price_sent_mb',
                  'pe.amnd_state',
                  'pe.status',
                  'dt.message_name',
                  'dt.message_code',
              ]);
        
        $query->andWhere(['p.amnd_state' => 1]);
        $query->andWhere(['p.status' => 1]);
        $query->andWhere(['pe.amnd_state' => 1]);
        $query->andWhere(['pe.status' => 1]);
    }
    
    public function applyAmountExtFilters($query)
    {      
        $query->joinWith(['prmTariffsExt pe'])
              ->select([
                  'p.trf_id',
                  'p.msg_type_id',
                  'pe.trf_prm_id',
                  'pe.docs_cnt_from',
                  'pe.docs_cnt_to',
                  'pe.price_sent_doc',
              ]);
        
        $query->andWhere(['p.amnd_state' => 1]);
        $query->andWhere(['p.status' => 1]);
        $query->andWhere(['pe.amnd_state' => 1]);
        $query->andWhere(['pe.status' => 1]);
    }
    
    private function applyStatusFilter($query)
    {
        
        if ($this->amnd_state === null || $this->amnd_state === '') {
            return;
        }

        if ($this->amnd_state == static::STATUS_NOT_DELETED) {
            $query->andWhere(['!=', 'amnd_state', static::STATUS_DELETED]);
        } else {
            $query->andWhere(['amnd_state' => $this->amnd_stade]);
        }
    }
    
    private function countDuplicates($queryCount)
    {
        $queryCount->select(['pe.trf_prm_id', 'COUNT(*)'])
                   ->groupBy('pe.trf_prm_id')
                   ->having('COUNT(*) >= 1');
    }
}
