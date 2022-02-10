<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use XMLReader;
use app\models\GeoModel;
use app\models\CadastrModel;
use Yii;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class GeoController extends Controller
{
    public $file;

    public function options($actionID)
    {
        return ['file'];
    }

    public function optionAliases()
    {
        return ['f' => 'file'];
    }

    public function actionImportGeo()
    {
        if(empty($this->file) || 1 == $this->file ){
            $this->stderr("Фай не указан\n", Console::BOLD);

            return ExitCode::UNSPECIFIED_ERROR;

        }
        if(!is_file($this->file)){
            $this->stderr("Фай $this->file не найден\n", Console::BOLD);

            return ExitCode::UNSPECIFIED_ERROR;
        }
        //echo $this->file . "\n";

        $xml = simplexml_load_file($this->file);
        if(!$xml){
            $this->stderr("Фай $this->file содержит ошибки\n", Console::BOLD);

            return ExitCode::UNSPECIFIED_ERROR;
        }
        $root = $xml->Document;
        if(empty($root)){
            $root = $xml->kml;
        }
        if(empty($root)){
            $this->stderr("Фай $this->file отсутствует корень\n", Console::BOLD);

            return ExitCode::UNSPECIFIED_ERROR;
        }
        //$root = $xml->Document;
        $polygon = $root->Placemark->Polygon;
        $coordinates = (string)$polygon->outerBoundaryIs->LinearRing->coordinates;
        if(empty($coordinates)){
            $this->stderr("Фай $this->file нет координат\n", Console::BOLD);

            return ExitCode::UNSPECIFIED_ERROR;
        }
        $coordinates = trim($coordinates);
        $coordinates = str_replace('  ', ' ', $coordinates);
        $coordinateArray = explode(' ', $coordinates);

        foreach($coordinateArray as $key => $value){
            $coordinateArray[$key] = explode(',', $value);
        }
        //print_r($root);


        $model = new GeoModel;
        $model->name = $root->Placemark->name;
        $model->geometry = [$coordinateArray];
        $model->save();
        
        echo ('Зона импортирована с именем '.$model->name);

        return ExitCode::OK;
    }

    public function actionImportCadastr()
    {
        if(empty($this->file) || 1 == $this->file ){
            $this->stderr("Фай не указан\n", Console::BOLD);

            return ExitCode::UNSPECIFIED_ERROR;

        }
        if(!is_file($this->file)){
            $this->stderr("Фай $this->file не найден\n", Console::BOLD);

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $row = 1;
        if (($handle = fopen($this->file, "r")) !== FALSE) {
            while (($dataCsv = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if(empty($dataCsv[0])){
                    continue;
                }
                if(CadastrModel::find()->where(
                    ['number' => $dataCsv[0]]
                )->exists()){
                    continue;
                }
                $url = getenv('CADASTR_API_URL')
                    .'?clientId='.urlencode(getenv('CADASTR_API_CLIENT_ID'))
                    .'&cadastralNumber='.urlencode($dataCsv[0]);
                $result = file_get_contents($url);
                if(empty($result)){
                    $this->stderr("Номер ".$dataCsv[0]." не найден\n", Console::BOLD);
                    continue;
                }
                $parse = json_decode($result);
                if(empty($parse->data) || empty($parse->status) || true != $parse->status ){
                    $this->stderr("Номер ".$dataCsv[0]." не разложился\n", Console::BOLD);
                    continue;
                }

                $cadastr = new CadastrModel;
                $cadastr->number = (string)$dataCsv[0];
                $cadastr->lat = (string)$parse->data->lat;
                $cadastr->lng = (string)$parse->data->lng;
                if ($cadastr->validate()) {
                    $cadastr->save();
                } else {
                    foreach($cadastr->errors as $fieldOne){
                        foreach($fieldOne as $errorOne){
                            $this->stderr("Номер ".$dataCsv[0]." ошибка $errorOne\n", Console::BOLD);
                        }
                    }
                }
            }
        }
    }
    
    public function getCadastrIsContain($name, $isContain = true)
    {
        if(empty($name)){
            $this->stderr("Не задано имя зоны\n", Console::BOLD);
            return ExitCode::UNSPECIFIED_ERROR;
        }
        $model = GeoModel::find()->where(['name' => $name])->one(); 
        if(empty($model)){
            $this->stderr("Не найдено зоны\n", Console::BOLD);
            return ExitCode::UNSPECIFIED_ERROR;
        }        
        $result = [];
        $cadastrAll = CadastrModel::find()->all();
        foreach($cadastrAll as $cadastrOne){
            $dis = (new \yii\db\Query())//GeoModel::find()                    
                ->select(['dis'=>'ST_Contains(geometry, ST_SetSRID(ST_MakePoint('.$cadastrOne->lng.','.$cadastrOne->lat.'),4326))'])
                ->where(['name' => $name])
                ->from(GeoModel::tableName())
                ->one();
            if($dis['dis'] && $isContain){
                $result[] =  $cadastrOne->number;
            }
            if(!$dis['dis'] && !$isContain){
                $result[] =  $cadastrOne->number;
            }
        }     
        return $result;
    }
    
    public function actionCheckInZone($name)
    {
        $inCont = $this->getCadastrIsContain($name);
        foreach($inCont as $value){
            $this->stdout($value."\n", Console::NORMAL);
        }
        
    }    

    public function actionCheckOutZone($name)
    {
        $inCont = $this->getCadastrIsContain($name,false);
        foreach($inCont as $value){
            $this->stdout($value."\n", Console::NORMAL);
        }        
    }    

    public function actionGeoInZone($name)
    {
        if(empty($name)){
            $this->stderr("Не задано имя зоны\n", Console::BOLD);
            return ExitCode::UNSPECIFIED_ERROR;
        }        
        $model = GeoModel::find()->where(['name' => $name])->one(); 
        if(empty($model)){
            $this->stderr("Не найдено зоны\n", Console::BOLD);
            return ExitCode::UNSPECIFIED_ERROR;
        }
        
        $carastr = $cadastrAll = CadastrModel::find()->all();
        $coordinates = [];
        foreach($carastr as $cadastrOne){
            //$this->stdout($value."\n", Console::NORMAL);
            $coordinates[] = [$cadastrOne->lng,$cadastrOne->lat];
            $dis = (new \yii\db\Query())
                ->select(['dis'=>'ST_AsGeoJSON(ST_MakePoint('.$cadastrOne->lng.','.$cadastrOne->lat.'))'])          
                ->one();    
            echo $dis['dis'];
        }      
        $dis = (new \yii\db\Query())
                ->select(['dis'=>'ST_AsGeoJSON(geometry)'])
                ->where(['name' => $name])
                ->from(GeoModel::tableName())            
                ->one();
            
        
        echo $dis['dis'];//->toGeoJson('MultiPoint', $coordinates, 4326);
        
    }    
    


}
