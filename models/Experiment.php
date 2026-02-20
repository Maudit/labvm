<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use DateTime;
use yii\db\ActiveQuery;
use yii\base\InvalidArgumentException;
use yii\validators\DateValidator;

/**
 * This is the model class for table "experiment".
 *
 * @property int $id
 * @property string $name
 * @property int $protocol_id Protocollo
 * @property string $execution_date Data di esecuzione
 * @property int $created_at Creato il
 * @property int $updated_at Modificato il
 * @property int $created_by Creato da
 * @property int $updated_by Modificato da
 *
 * @property User $createdBy
 * @property Protocol $protocol
 * @property Result[] $results
 * @property Test[] $tests
 * @property User $updatedBy
 */
class Experiment extends \yii\db\ActiveRecord
{

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'experiment';
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'name',
                'protocol_id',
                'execution_date'
            ],
            self::SCENARIO_UPDATE => [
                'name',
                'protocol_id',
                'execution_date'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'protocol_id', 'execution_date'], 'required'],
            [['protocol_id'], 'integer'],
            [['execution_date'], 'safe'],
            [['name'], 'string', 'max' => 255],
            //[['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['protocol_id'], 'exist', 'skipOnError' => true, 'targetClass' => Protocol::class, 'targetAttribute' => ['protocol_id' => 'id']],
            //[['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
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
        if ($this->attachmentMultiwell) {
            $counter +=1;
        }
        if ($this->attachmentResult) {
            $counter +=1;
        }
        if ($this->attachmentAnalysis) {
            $counter +=1;
        }
        return $counter;
    }

    public function projectCount()
    {
        return $this->getProjects()->count();
    }

    public function beforeSave($insert)
    {
        // Converte la data di acquisto nel formato anno-mese-giorno, richiesto da MySQL per il campo data
        //TODO: valutare lo scenario e l'action.
        $date = DateTime::createFromFormat('d/m/Y', $this->execution_date);
        $this->execution_date = $date->format('Y-m-d');

        return parent::beforeSave($insert);
    }


    function afterFind()
    {
        parent::afterFind();

        // Converte la data di acquisto nel formato giorno/mese/anno, usato nei widget per il campo data
        //TODO: valutare lo scenario e l'action.
        if ($this->execution_date) {
            $date = DateTime::createFromFormat('Y-m-d', $this->execution_date);
            $this->execution_date = $date->format('d/m/Y');
        }
    }

    /**
     * Gets query for [[Protocol]].
     *
     * @return \yii\db\ActiveQuery|ProtocolQuery
     */
    public function getProtocol()
    {
        return $this->hasOne(Protocol::class, ['id' => 'protocol_id']);
    }


    /**
     * Gets query for [[Tests]].
     *
     * @return \yii\db\ActiveQuery|TestQuery
     */
    public function getTests()
    {
        return $this->hasMany(Test::class, ['experiment_id' => 'id']);
    }

    /**
     * Ritorna i compound testati in questo esperimento tramite la relazione definita in tests
     * 
     * @return ActiveQuery 
     * @throws InvalidArgumentException 
     */
    public function getCompounds()
    {
        return $this->hasMany(Compound::class, ['id' => 'compound_id'])->via('tests');
        // Alternativamente: ->viaTable('test', ['experiment_id' => 'id']);
    }

    /**
     * Gets query for [[AttachmentAnalysis]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttachmentAnalysis()
    {
        return $this->hasOne(AttachmentAnalysis::class, ['experiment_id' => 'id']);
    }

    /**
     * Gets query for [[AttachmentMultiwell]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttachmentMultiwell()
    {
        return $this->hasOne(AttachmentMultiwell::class, ['experiment_id' => 'id']);
    }

    /**
     * Gets query for [[AttachmentResult]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttachmentResult()
    {
        return $this->hasOne(AttachmentResult::class, ['experiment_id' => 'id']);
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
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[ExperimentProjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExperimentProjects()
    {
        return $this->hasMany(ExperimentProject::class, ['experiment_id' => 'id']);
    }

    /**
     * Gets query for [[Projects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjects()
    {
        return $this->hasMany(Project::class, ['id' => 'project_id'])->via('experimentProjects');
    }


    /**
     * {@inheritdoc}
     * @return ExperimentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ExperimentQuery(get_called_class());
    }
}
