<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Test]].
 *
 * @see Test
 */
class TestQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Test[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Test|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * Seleziona solo i risultati che sono stati cestinati.
     * @return $this 
     */
    /*
    public function trashed()
    {
        return $this->andOnCondition(['test.deleted' => true]);
    }
    */

    /**
     * Seleziona solo i risultati che non sono stati cestinati.
     * @return $this 
     */
    /*
    public function not_trashed()
    {
        return $this->andOnCondition(['test.deleted' => false]);
    }
    */
}
