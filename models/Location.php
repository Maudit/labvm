<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use app\modules\account\models\User;

/**
 * This is the model class for table "location".
 *
 * @property int $id
 * @property string $name Nome
 * @property int $created_at Creato il
 * @property int $updated_at Modificato il
 * @property int $created_by Creato da
 * @property int $updated_by Modificato da
 *
 * @property User $createdBy
 * @property Product[] $products
 * @property Solution[] $solutions
 * @property User $updatedBy
 */
class Location extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'location';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name',], 'required'],
            [['name'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
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

    /**
     * Ritorna un array di posizioni nella forma id=>'name'
     */
    public static function getList()
    {
        return self::find()->select(['name'])->indexBy('id')->column();
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
     * Gets query for [[Compounds]].
     *
     * @return \yii\db\ActiveQuery|ProductQuery
     */
    public function getCompounds()
    {
        return $this->hasMany(Compound::class, ['location_id' => 'id']);
    }

    /**
     * Gets query for [[Solutions]].
     *
     * @return \yii\db\ActiveQuery|SolutionQuery
     */
    /* Forse è del vecchio codice?
    public function getSolutions()
    {
        return $this->hasMany(Solution::class, ['location_id' => 'id']);
    }
    */

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
     * @return LocationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LocationQuery(get_called_class());
    }
}
