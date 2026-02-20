<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Test;
use kartik\daterange\DateRangeBehavior;

/**
 * TestSearch represents the model behind the search form of `app\models\Test`.
 */
class TestSearch extends Test
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
            [['id', 'experiment_id', 'compound_id','compound.physical_form_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [
                [
                    'compound.deposit_date',
                    'compound.name',
                    'compound.molecular_formula',
                    'compound.manufacturer.name',
                    'compound.location.name'
                ],
                'safe'
            ],
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
            'compound.physical_form_id',
            'compound.deposit_date',
            'compound.name',
            'compound.formula',
            'compound.manufacturer.name',
            'compound.location.name'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'experiment_id' => 'Esperimento',
            'compound_id' => 'Composto',
            'created_at' => 'Creato il',
            'updated_at' => 'Modificato il',
            'created_by' => 'Creato da',
            'updated_by' => 'Modificato da',
            'compound.deposit_date'=>'Data di deposito',
            'compound.physical_form_id' => 'Forma',
            'compound.name' => 'Nome',
            'compound.formula' => 'Molecola',
            'compound.manufacturer.name' => 'Produttore',
            'compound.location.name' => 'Posizione',
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
        $query = TestSearch::find()->alias('test');
        $query->select([
            'test.id',
            'test.experiment_id',
            'test.compound_id',
            'test.created_at',
            'test.updated_at',
            'test.created_by',
            'test.updated_by',
        ]);
        // composto
        $query->joinWith([
            'compound' => function ($query) {
                $query->alias('compound');
                $query->select([
                    'compound.id',
                    'compound.name',
                    'compound.formula',
                    //'compound.parent_id',
                    'compound.manufacturer_id',
                    'compound.deposit_date',
                    'compound.exhaustion_date',
                    'compound.in_stock',
                    'compound.physical_form_id',
                    'compound.location_id',
                    'compound.notes',
                ]);
                // molecola del composto
                /*
                $query->joinWith([
                    'molecule' => function ($query) {
                        $query->alias('molecule');
                        $query->select([
                            'molecule.id',
                            'molecule.name',
                            'molecule.molecular_formula',
                        ]);
                    }
                ]);
                */
                // produttore del composto
                $query->joinWith([
                    'manufacturer' => function ($query) {
                        $query->alias('manufacturer');
                        $query->select([
                            'manufacturer.id',
                            'manufacturer.name',
                        ]);
                    }
                ]);
                // Posizione del composto
                $query->joinWith([
                    'location' => function ($query) {
                        $query->alias('location');
                        $query->select([
                            'location.id',
                            'location.name',
                        ]);
                    }
                ]);
            }
        ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => ['defaultOrder' => ['compound_id' => SORT_DESC]],
        ]);

        
        $dataProvider->sort->attributes['compound.name'] = [
            'asc' => ['compound.name' => SORT_ASC],
            'desc' => ['compound.name' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['compound.formula'] = [
            'asc' => ['compound.formula' => SORT_ASC],
            'desc' => ['compound.formula' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['compound.manufacturer.name'] = [
            'asc' => ['manufacturer.name' => SORT_ASC],
            'desc' => ['manufacturer.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['compound.location.name'] = [
            'asc' => ['location.name' => SORT_ASC],
            'desc' => ['location.name' => SORT_DESC],
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
            'experiment_id' => $this->experiment_id,
            'compound_id' => $this->compound_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'compound.physical_form_id'=> $this->getAttribute('compound.physical_form_id'),
        ]);

        $query->andFilterWhere(['like', 'compound.name', $this->getAttribute('compound.name')]);
        $query->andFilterWhere(['like', 'compound.formula', $this->getAttribute('compound.formula')]);
        $query->andFilterWhere(['like', 'manufacturer.name', $this->getAttribute('compound.manufacturer.name')]);
        $query->andFilterWhere(['like', 'location.name', $this->getAttribute('compound.location.name')]);

        if ($this->createTimeRange) {
            $query->andFilterWhere(['>=', 'compound.deposit_date', $this->createTimeStart])
                ->andFilterWhere(['<=', 'compound.deposit_date', $this->createTimeEnd]);
        }

        return $dataProvider;
    }
}
