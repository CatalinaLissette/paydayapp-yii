<?php


namespace app\api\v1\controllers;


use app\models\Commune;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;

class CommuneController extends ActiveController
{
    public $modelClass = Commune::class;

    public function behaviors()
    {
        return ArrayHelper::merge([
            'cors' => [
                'class' => Cors::class
            ]
        ], parent::behaviors());
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
        if (Yii::$app->getRequest()->getMethod() === 'OPTIONS') {
            Yii::debug('is options');
            Yii::$app->getResponse()->getHeaders()->set('Allow', 'POST GET PUT');
            Yii::$app->end();
            return;
        }
        return parent::beforeAction($action);
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