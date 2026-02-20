<?php

namespace app\models;

use app\modules\account\models\User as BaseUser;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $name Nome
 * @property string $surname Cognome
 * @property string $username Username
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $email E-mail
 * @property int $status Status
 * @property int $created_at Creato il
 * @property int $updated_at Modificato il
 * @property string|null $verification_token
 *
 * @property Experiment[] $experiments
 * @property Experiment[] $experiments0
 * @property Location[] $locations
 * @property Location[] $locations0
 * @property Manufacturer[] $manufacturers
 * @property Manufacturer[] $manufacturers0
 * @property Molecule[] $molecules
 * @property Molecule[] $molecules0
 * @property Result[] $results
 * @property Result[] $results0
 * @property Test[] $tests
 * @property Test[] $tests0
 */

class User extends BaseUser
{
    /**
    * {@inheritdoc}
    */
   public function attributeLabels()
   {
       return [
           'id' => 'ID',
           'name' => 'Nome',
           'surname' => 'Cognome',
           'username' => 'Username',
           'auth_key' => 'Auth Key',
           'password_hash' => 'Password Hash',
           'password_reset_token' => 'Password Reset Token',
           'email' => 'E-mail',
           'status' => 'Status',
           'created_at' => 'Creato il',
           'updated_at' => 'Modificato il',
           'verification_token' => 'Verification Token',
       ];
   }

   /**
    * Gets query for [[Experiments]].
    *
    * @return \yii\db\ActiveQuery
    */
   public function getCreatedExperiments()
   {
       return $this->hasMany(Experiment::class, ['created_by' => 'id']);
   }

   /**
    * Gets query for [[Experiments0]].
    *
    * @return \yii\db\ActiveQuery
    */
   public function getUpdatedExperiments()
   {
       return $this->hasMany(Experiment::class, ['updated_by' => 'id']);
   }

   /**
    * Gets query for [[Locations]].
    *
    * @return \yii\db\ActiveQuery
    */
   public function getCreatedLocations()
   {
       return $this->hasMany(Location::class, ['created_by' => 'id']);
   }

   /**
    * Gets query for [[Locations0]].
    *
    * @return \yii\db\ActiveQuery
    */
   public function getUpdatedLocations()
   {
       return $this->hasMany(Location::class, ['updated_by' => 'id']);
   }

   /**
    * Gets query for [[Manufacturers]].
    *
    * @return \yii\db\ActiveQuery
    */
   public function getCreatedManufacturers()
   {
       return $this->hasMany(Manufacturer::class, ['created_by' => 'id']);
   }

   /**
    * Gets query for [[Manufacturers0]].
    *
    * @return \yii\db\ActiveQuery
    */
   public function getUpdatedManufacturers()
   {
       return $this->hasMany(Manufacturer::class, ['updated_by' => 'id']);
   }

   /**
    * Gets query for [[Molecules]].
    *
    * @return \yii\db\ActiveQuery
    */
   public function getCreatedMolecules()
   {
       return $this->hasMany(Molecule::class, ['created_by' => 'id']);
   }

   /**
    * Gets query for [[Molecules0]].
    *
    * @return \yii\db\ActiveQuery
    */
   public function getUpdatedMolecules()
   {
       return $this->hasMany(Molecule::class, ['updated_by' => 'id']);
   }

   /**
    * Gets query for [[Results]].
    *
    * @return \yii\db\ActiveQuery
    */
   public function getCreatedResults()
   {
       return $this->hasMany(Result::class, ['created_by' => 'id']);
   }

   /**
    * Gets query for [[Results0]].
    *
    * @return \yii\db\ActiveQuery
    */
   public function getUpdatedResults()
   {
       return $this->hasMany(Result::class, ['updated_by' => 'id']);
   }

   /**
    * Gets query for [[Tests]].
    *
    * @return \yii\db\ActiveQuery
    */
   public function getCreatedTests()
   {
       return $this->hasMany(Test::class, ['created_by' => 'id']);
   }

   /**
    * Gets query for [[Tests0]].
    *
    * @return \yii\db\ActiveQuery
    */
   public function getUpdatedTests()
   {
       return $this->hasMany(Test::class, ['updated_by' => 'id']);
   }
}
