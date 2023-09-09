<?php


namespace app\api\v1\controllers;


use app\models\Commerce;
use app\models\ProviderHasCommerce;
use Yii;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;

class CommerceController extends SafeController
{
    public $modelClass = Commerce::class;

    public function actionEnroll()
    {
        $model = ProviderHasCommerce::enroll($this->request->post());
        $this->response->format = 'json';
        if ($model->save()) {
             $this->response->setStatusCode(201);
            return ['created' => true];
        }
        throw new BadRequestHttpException(Json::encode($model->errors));
    }
}