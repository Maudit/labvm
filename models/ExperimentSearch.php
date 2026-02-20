<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Experiment;
use kartik\daterange\DateRangeBehavior;

/**
 * ExperimentSearch represents the model behind the search form of `app\models\Experiment`.
 */
class ExperimentSearch extends Experiment
{
    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'protocol_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name', 'execution_date', 'protocol.name'], 'safe'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
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
     * {@inheritdoc}
     */
    public function attributes()
    {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), [
            'protocol.name',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nome',
            'protocol_id' => 'Protocollo',
            'execution_date' => 'Data di esecuzione',
            'created_at' => 'Creato il',
            'updated_at' => 'Modificato il',
            'created_by' => 'Creato da',
            'updated_by' => 'Modificato da',
            'protocol.name'=>'Protocollo'
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => DateRangeBehavior::class,
                'attribute' => 'createTimeRange',
                'dateStartAttribute' => 'createTimeStart',
                'dateStartFormat' => 'Y-m-d',
                'dateEndAttribute' => 'createTimeEnd',
                'dateEndFormat' => 'Y-m-d',
            ]
        ];
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
        $query = ExperimentSearch::find()->alias('experiment');

        $query->select([
            'experiment.id',
            'experiment.name',
            'experiment.protocol_id',
            'experiment.execution_date',
            'experiment.created_at',
            'experiment.updated_at',
            'experiment.created_by',
            'experiment.updated_by',
        ]);

        $query->joinWith([
            'protocol' => function ($query){
                $query->alias('protocol');
                $query->select([
                    'protocol.id',
                    'protocol.name',
                ]);
            }
        ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort'=> ['defaultOrder' => ['execution_date' => SORT_DESC]],
        ]);

        $dataProvider->sort->attributes['protocol.name'] = [
            'asc' => ['protocol.name' => SORT_ASC],
            'desc' => ['protocol.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'protocol_id' => $this->protocol_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'experiment.name', $this->name]);
        $query->andFilterWhere(['like', 'protocol.name', $this->getAttribute('protocol.name')]);

        if ($this->createTimeRange) {
            $query->andFilterWhere(['>=', 'execution_date', $this->createTimeStart])
                ->andFilterWhere(['<=', 'execution_date', $this->createTimeEnd]);
        }

        return $dataProvider;
    }
}
