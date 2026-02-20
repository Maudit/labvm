<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "experiment_project".
 *
 * @property int $experiment_id
 * @property int $project_id
 *
 * @property Experiment $experiment
 * @property Project $project
 */
class ExperimentProject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'experiment_project';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['experiment_id', 'project_id'], 'required'],
            [['experiment_id', 'project_id'], 'integer'],
            [['experiment_id', 'project_id'], 'unique', 'targetAttribute' => ['experiment_id', 'project_id']],
            [['experiment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Experiment::class, 'targetAttribute' => ['experiment_id' => 'id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['project_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'experiment_id' => 'Esperimento',
            'project_id' => 'Progetto',
        ];
    }

    /**
     * Gets query for [[Experiment]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExperiment()
    {
        return $this->hasOne(Experiment::class, ['id' => 'experiment_id']);
    }

    /**
     * Gets query for [[Project]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::class, ['id' => 'project_id']);
    }

}
