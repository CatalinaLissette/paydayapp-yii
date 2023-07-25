<?php


namespace app\api\v1\controllers;


use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\rest\ActiveController;

class SafeController extends ActiveController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class
        ];
        $behaviors['cors'] = [
            'class' => Cors::class
        ];
        return $behaviors;
    }
}