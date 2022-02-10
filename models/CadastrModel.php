<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cadastrs".
 *
 * @property int $id
 * @property string $number
 * @property string $lat
 * @property string $lng
 */
class CadastrModel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cadastrs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number', 'lat', 'lng'], 'required'],
            [['number', 'lat', 'lng'], 'string', 'max' => 255],
            [['number'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Number',
            'lat' => 'Latintude',
            'lng' => 'Longitude',
        ];
    }

    /**
     * {@inheritdoc}
     * @return CadastrsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CadastrsQuery(get_called_class());
    }
}
