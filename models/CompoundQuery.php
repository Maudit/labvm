<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Compound]].
 *
 * @see Compound
 */
class CompoundQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Compound[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Compound|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * Seleziona solo i composti che sono stati cestinati.
     * @return $this 
     */
    /*
    public function trashed()
    {
        return $this->andOnCondition(['deleted' => true]);
    }
    */
    /**
     * Seleziona solo i composti che non sono stati cestinati.
     * @return $this 
     */
    /*
    public function not_trashed()
    {
        return $this->andOnCondition(['deleted' => false]);
    }
    */
    /**
     * Seleziona solo i composti disponibili.
     * @return $this 
     */
    public function available()
    {
        return $this->andOnCondition(['in_stock' => true]);
    }

    /**
     * Seleziona solo i composti esauriti.
     * @return $this 
     */
    public function unavailable()
    {
        return $this->andOnCondition(['in_stock' => false]);
    }
}
