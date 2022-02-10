<?php

namespace app\controllers;

use Yii;
use yii\web\Response;
use app\models\CadastrModel;
use app\models\GeoModel;

class ApiController extends \yii\web\Controller
{

    public function actionMap()
    {
        $data = ['errors' => ['ошибка не определена']];
        $cadastrNumber = Yii::$app->request->get('cadastr_number');
        if(empty($cadastrNumber)){
            Yii::$app->response->statusCode = 422;
            $data = ['errors' => ['cadastr_number' =>['Параметр обязательен']]];
        } else {
            $cadastrOne = CadastrModel::find()->where(['number' => $cadastrNumber])->one();
        }
        if(empty($cadastrOne)){
            Yii::$app->response->statusCode = 422;
            $data = ['errors' => ['cadastr_number' =>['Запись не найдена']]];
        } else {
            $dis = (new \yii\db\Query())
                ->select(['dis'=>'ST_AsGeoJSON(ST_MakePoint('.$cadastrOne->lng.','.$cadastrOne->lat.'))'])
                ->one();
            $pointArray = json_decode($dis['dis'], true);
        }

        $zoneName = Yii::$app->request->get('zone_name');
        if(empty($zoneName)){
            Yii::$app->response->statusCode = 422;
            $data = ['errors' => ['zone_name' =>['Параметр обязательен']]];
        } else {
            $zone = (new \yii\db\Query())
                ->select(['dis'=>'ST_AsGeoJSON(geometry)'])
                ->where(['name' => $zoneName])
                ->from(GeoModel::tableName())
                ->one();
        }
        if(empty($zone)){
            Yii::$app->response->statusCode = 422;
            $data = ['errors' => ['zone_name' =>['Запись не найдена']]];
        } else {
            $zoneArray = json_decode($zone['dis'], true);;
        }

        if(!empty($pointArray) && !empty($zoneArray)){
            $data = [
                "type" => "FeatureCollection",
                "metadata" => [
                    "name" => "Кадастровый номер",
                    "description" => "",
                ],
                "features" => [
                    [
                        'type' => 'Feature',
                        'geometry' => $pointArray,
                        "properties" => ["description" => $cadastrNumber],
                    ],
                    [
                        'type' => 'Feature',
                        'geometry' => $zoneArray,
                        "properties" => ["description" => 'Участок'],
                    ],
                ]
            ];
        }




        Yii::$app->response->format = Response::FORMAT_JSON;
        return $data;
    }

}
