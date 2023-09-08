<?php


namespace app\api\v1\controllers;


use app\models\Provider;
use yii\rest\ActiveController;

class ProviderController extends SafeController
{
    public $modelClass = Provider::class;
}