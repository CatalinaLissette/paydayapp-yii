<?php


namespace app\api\v1\controllers;


use app\models\Provider;
use yii\rest\ActiveController;

class ProviderController extends ActiveController
{
    public $modelClass = Provider::class;
}