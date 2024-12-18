<?php


namespace app\api\v1\controllers;


use app\models\Commune;
use app\models\Region;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;

class RegionController extends ActiveController
{
    public $modelClass = Region::class;

    public function behaviors()
    {
        return ArrayHelper::merge([
            'cors' => [
                'class' => Cors::class
            ]
        ], parent::behaviors());
    }





    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        return $actions;
    }




    public function actionCreate()
    {

        $model = new $this->modelClass;

        $data = $this->request->post();

        $model->load(['Region' => $data]);

        if($model->save()) {
            return $model;
        }

        return $model->getErrors();

    }

}
