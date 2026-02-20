<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "result".
 *
 * @property int $id
 * @property int $experiment_id
 * @property int $protocol_id
 * @property int $test_id Test
 * @property float $value
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property User $createdBy
 * @property Experiment $experiment
 * @property Protocol $protocol
 * @property Test $test
 * @property User $updatedBy
 */
class Result extends \yii\db\ActiveRecord
{

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'result';
    }


    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'test_id',
                'value',
            ],
            self::SCENARIO_UPDATE => [
                'value',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['test_id', 'value'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE],],
            [['test_id'], 'integer', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['value'], 'number', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => Test::class, 'targetAttribute' => ['test_id' => 'id'], 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'test_id' => 'Test',
            'value' => 'Valore',
            'created_at' => 'il giorno / ora',
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
     * Gets query for [[Test]].
     *
     * @return \yii\db\ActiveQuery|TestQuery
     */
    public function getTest()
    {
        return $this->hasOne(Test::class, ['id' => 'test_id']);
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
     * @return ResultQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ResultQuery(get_called_class());
    }
}
