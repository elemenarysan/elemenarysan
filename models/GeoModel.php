<?php
namespace app\models;

use yii\db\ActiveRecord;
use nanson\postgis\behaviors\GeometryBehavior;

class GeoModel extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%geo}}';
    }
    
    public function behaviors()
    {
        return [
            [
                'class' => GeometryBehavior::className(),
                'type' => GeometryBehavior::GEOMETRY_POLYGON,
                'attribute' => 'geometry',
                // skip attribute if it was not selected as Geo Json (by PostgisQueryTrait), because it requires a separate query.
                'skipAfterFindPostgis' => true,
            ],
        ];
    }


}

?>
