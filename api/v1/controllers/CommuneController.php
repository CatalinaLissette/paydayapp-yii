<?php


namespace app\api\v1\controllers;


use app\models\Commune;
use yii\filters\Cors;
use yii\rest\ActiveController;

class CommuneController extends ActiveController
{
    public $modelClass = Commune::class;

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

    public function actionCreate($region_id)
    {

        $model = new $this->modelClass;

        $data = $this->request->post();

        $model->load(['Commune' => $data]);

        $model->region_id = $region_id;



        if($model->save()) {
            return $model;
        }

        return $model->getErrors();

    }

    public function actionSearchByRegionId($region_id){
        $model = new $this->modelClass;
        return $model::find()->where(['region_id' => $region_id])->all();

    }
}