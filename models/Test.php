<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "test".
 *
 * @property int $id
 * @property int $experiment_id Esperimento
 * @property int $compound_id Composto
 * @property string|null $file_main_directory Directory contenitore per l'upload
 * @property string|null $file_sub_directory Directory contenente il file e le sue elaborazioni
 * @property string|null $file_name Nome del file
 * @property string|null $file_ext Estensione del file
 * @property int $created_at Creato il
 * @property int $updated_at Modificato il
 * @property int $created_by Creato da
 * @property int $updated_by Modificato da
 *
 * @property Compound $compound
 * @property AttachmentGraph $attachmentGraph
 * @property User $createdBy
 * @property Experiment $experiment
 * @property Result[] $results
 * @property User $updatedBy
 */
class Test extends \yii\db\ActiveRecord
{

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'test';
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'experiment_id',
                'compound_id',
            ],
            self::SCENARIO_UPDATE => [
                'experiment_id',
                'compound_id',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['experiment_id', 'compound_id'], 'required'],
            [['experiment_id', 'compound_id'], 'integer'],
            //[['experiment_id', 'compound_id'], 'unique', 'targetAttribute' => ['experiment_id', 'compound_id'], 'message' => 'This compound is already associated with this experiment.'],
            [['compound_id'], 'exist', 'skipOnError' => true, 'targetClass' => Compound::class, 'targetAttribute' => ['compound_id' => 'id']],
            [['experiment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Experiment::class, 'targetAttribute' => ['experiment_id' => 'id']],
        ];
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    public function attachmentCount()
    {
        $counter = 0;
        if ($this->attachmentGraph) {
            $counter +=1;
        }
        return $counter;
    }

    /**
     * Gets query for [[Compound]].
     *
     * @return \yii\db\ActiveQuery|CompoundQuery
     */
    public function getCompound()
    {
        return $this->hasOne(Compound::class, ['id' => 'compound_id']);
    }

    /**
     * Gets query for [[AttachmentGraph]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttachmentGraph()
    {
        return $this->hasOne(AttachmentGraph::class, ['test_id' => 'id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[Experiment]].
     *
     * @return \yii\db\ActiveQuery|ExperimentQuery
     */
    public function getExperiment()
    {
        return $this->hasOne(Experiment::class, ['id' => 'experiment_id']);
    }

    /**
     * Gets query for [[Results]].
     *
     * @return \yii\db\ActiveQuery|ResultQuery
     */
    public function getResults()
    {
        return $this->hasMany(Result::class, ['test_id' => 'id']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * {@inheritdoc}
     * @return TestQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TestQuery(get_called_class());
    }
}
