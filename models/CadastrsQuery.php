<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[CadastrModel]].
 *
 * @see CadastrModel
 */
class CadastrsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return CadastrModel[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return CadastrModel|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
