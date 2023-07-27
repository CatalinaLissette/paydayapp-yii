<?php


namespace app\api\v1\controllers;


use app\models\Region;
use yii\filters\Cors;
use yii\rest\ActiveController;

class RegionController extends ActiveController
{
    public $modelClass = Region::class;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['cors'] = [
            'class' => Cors::class
        ];
        return $behaviors;
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
