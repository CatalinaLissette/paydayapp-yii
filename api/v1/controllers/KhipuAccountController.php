<?php


namespace app\api\v1\controllers;


use app\models\KhipuAccount;
use yii\rest\ActiveController;

class KhipuAccountController extends ActiveController
{
    public $modelClass = KhipuAccount::class;

    public function actionCreate()
    {
        $model = new $this->modelClass;

        $data = $this->request->post();
        print_r($data);

        $model->load(['KhipuAccount' => $data]);

        if($model->save()) {
            return $model;
        }

        return $model->getErrors();

    }

    public function actionSearchByProviderId($provider_id){
        $model = new $this->modelClass;
        return $model::find()->where(['provider_id' => $provider_id])->all();

    }
}