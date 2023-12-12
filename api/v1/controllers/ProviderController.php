<?php


namespace app\api\v1\controllers;


use app\models\Provider;
use yii\filters\Cors;
use yii\rest\ActiveController;

class ProviderController extends ActiveController
{
    public $modelClass = Provider::class;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['cors'] = [
            'class' => Cors::class,
        ];

        return $behaviors;
    }
}