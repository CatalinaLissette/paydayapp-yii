<?php


namespace app\api\v1\controllers;


use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\rest\ActiveController;

class SafeController extends ActiveController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];
        $behaviors['cors'] = [
            'class' => Cors::class
        ];
        return $behaviors;
    }

    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
        ];
    }

    public function beforeAction($action)
    {
        if($this->request->isOptions) {
            return true;
        }
        return parent::beforeAction($action);
    }
}