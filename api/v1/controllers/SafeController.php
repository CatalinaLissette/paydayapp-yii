<?php


namespace app\api\v1\controllers;


use sizeg\jwt\JwtHttpBearerAuth;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\Response;

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

    protected function returnEmptyBody(int $status): void
    {
        $this->response->format = Response::FORMAT_JSON;
        $this->response->content = '';
        $this->response->setStatusCode($status);
    }
}