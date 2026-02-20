<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Compound;
use kartik\daterange\DateRangeBehavior;

/**
 * CompoundSearch rappresenta il modello di ricerca unificato per Compound.
 * Assorbe le funzionalità di ricerca per composti disponibili ed esauriti.
 */
class CompoundSearch extends Compound
{

    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;

    public $exhaustionTimeRange;
    public $exhaustionTimeStart;
    public $exhaustionTimeEnd;

    /**
     * {@inheritdoc}
     */
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
            ],
            // Aggiunto behavior per il range di esaurimento che era nel modello Unavailable
            [
                'class' => DateRangeBehavior::class,
                'attribute' => 'exhaustionTimeRange',
                'dateStartAttribute' => 'exhaustionTimeStart',
                'dateStartFormat' => 'Y-m-d',
                'dateEndAttribute' => 'exhaustionTimeEnd',
                'dateEndFormat' => 'Y-m-d',
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'parent_id', 'manufacturer_id', 'in_stock', 'physical_form_id', 'location_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name', 'formula', 'smiles', 'deposit_date', 'exhaustion_date', 'notes', 'manufacturer.name', 'location.name'], 'safe'],
            [['createTimeRange', 'exhaustionTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
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
            'manufacturer.name',
            'location.name'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        // add related labels to searchable attributes
        return array_merge(parent::attributeLabels(), [
            'manufacturer.name' => 'Produttore',
            'location.name' => 'Posizione'
        ]);
    }

    /**
     * Crea un'istanza di data provider con la query di ricerca applicata.
     *
     * @param array $params
     * @param array $excludeIds Gli ID da escludere (per la selezione nei Test)
     * @param string|int $statusFilter Filtra per stato (STATUS_AVAILABLE, STATUS_EXHAUSTED, STATUS_ALL)
     *
     * @return ActiveDataProvider
     */
    public function search($params, $excludeIds = [], $statusFilter = Compound::STATUS_ALL)
    {
        $query = Compound::find()->alias('compound');
        $query->select([
            'compound.id',
            'compound.parent_id',
            'compound.name',
            'compound.formula',
            'compound.smiles',
            'compound.manufacturer_id',
            'compound.deposit_date',
            'compound.exhaustion_date',
            'compound.in_stock',
            'compound.physical_form_id',
            'compound.location_id',
            'compound.notes',
            'compound.created_at',
            'compound.updated_at',
            'compound.created_by',
            'compound.updated_by',
        ]);

        // 1. Logica di filtraggio per Stato (Switch)
        switch ($statusFilter) {
            case Compound::STATUS_AVAILABLE:
                $query->andWhere(['compound.in_stock' => Compound::STATUS_AVAILABLE]);
                break;

            case Compound::STATUS_EXHAUSTED:
                $query->andWhere(['compound.in_stock' => Compound::STATUS_EXHAUSTED]);
                break;

            case Compound::STATUS_ALL:
            default:
                // Nessun filtro: mostra sia disponibili che esauriti
                break;
        }

        // 2. Esclusione Compound (per i Test)
        if (!empty($excludeIds)) {
            $query->andWhere(['not in', 'compound.id', $excludeIds]);
        }

        // JOIN WITH PRODUTTORE (Dettagliato)
        $query->joinWith([
            'manufacturer' => function ($query) {
                $query->alias('manufacturer');
                $query->select([
                    'manufacturer.id',
                    'manufacturer.name',
                ]);
            }
        ]);

        // JOIN WITH POSIZIONE (Dettagliato)
        $query->joinWith([
            'location' => function ($query) {
                $query->alias('location');
                $query->select([
                    'location.id',
                    'location.name',
                ]);
            }
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => ['defaultOrder' => ['deposit_date' => SORT_DESC]],
        ]);

        // Configurazione ordinamento
        $dataProvider->sort->attributes['manufacturer.name'] = [
            'asc' => ['manufacturer.name' => SORT_ASC],
            'desc' => ['manufacturer.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['location.name'] = [
            'asc' => ['location.name' => SORT_ASC],
            'desc' => ['location.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // CONDIZIONI DI FILTRO GRID
        $query->andFilterWhere([
            'compound.id' => $this->id,
            'compound.parent_id' => $this->parent_id,
            'manufacturer_id' => $this->manufacturer_id,
            'deposit_date' => $this->deposit_date,
            'exhaustion_date' => $this->exhaustion_date,
            'in_stock' => $this->in_stock,
            'physical_form_id' => $this->physical_form_id,
            'location_id' => $this->location_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'compound.name', $this->name])
            ->andFilterWhere(['like', 'compound.formula', $this->formula])
            ->andFilterWhere(['like', 'smiles', $this->smiles])
            ->andFilterWhere(['like', 'compound.notes', $this->notes])
            ->andFilterWhere(['like', 'manufacturer.name', $this->getAttribute('manufacturer.name')])
            ->andFilterWhere(['like', 'location.name', $this->getAttribute('location.name')]);

        
        // Range Date Deposito
        if ($this->createTimeRange) {
            $query->andFilterWhere(['>=', 'compound.deposit_date', $this->createTimeStart])
                ->andFilterWhere(['<=', 'compound.deposit_date', $this->createTimeEnd]);
        }

        // Range Date Esaurimento
        if ($this->exhaustionTimeRange) {
            $query->andFilterWhere(['>=', 'compound.exhaustion_date', $this->exhaustionTimeStart])
                ->andFilterWhere(['<=', 'compound.exhaustion_date', $this->exhaustionTimeEnd]); // Corretto qui
        }

        return $dataProvider;
    }
}
