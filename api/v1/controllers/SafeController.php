<?php


namespace app\api\v1\controllers;


use sizeg\jwt\JwtHttpBearerAuth;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\rest\Controller;

class SafeController extends ActiveController
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'cors' => [
                'class' => Cors::class
            ],
            'authenticator' => [
                'class' => JwtHttpBearerAuth::class
            ]
        ]);
    }
}