<?php

namespace app\models;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use app\modules\account\models\User;

use Yii;

/**
 * This is the model class for table "project".
 *
 * @property int $id
 * @property string $name Nome del progetto
 * @property int $created_at Creato il
 * @property int $updated_at Modificato il
 * @property int $created_by Creato da
 * @property int $updated_by Modificato da
 *
 * @property ExperimentProject[] $experimentProjects
 * @property Experiment[] $experiments
 */
class Project extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
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

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nome del progetto',
            'created_at' => 'Creato il',
            'updated_at' => 'Modificato il',
            'created_by' => 'Creato da',
            'updated_by' => 'Modificato da',
        ];
    }

    /**
     * Gets query for [[ExperimentProjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExperimentProjects()
    {
        return $this->hasMany(ExperimentProject::class, ['project_id' => 'id']);
    }

    /**
     * Gets query for [[Experiments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExperiments()
    {
        return $this->hasMany(Experiment::class, ['id' => 'experiment_id'])->via('experimentProjects');
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
}
