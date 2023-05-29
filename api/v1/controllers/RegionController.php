<?php


namespace app\api\v1\controllers;


use app\models\Region;
use yii\rest\ActiveController;

class RegionController extends ActiveController
{
    public $modelClass = Region::class;

}
