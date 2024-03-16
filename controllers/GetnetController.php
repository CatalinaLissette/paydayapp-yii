<?php

namespace app\controllers;

use app\services\GetNetClickService;
use yii\web\Controller;

class GetnetController extends Controller
{
    private GetNetClickService $service;

    public function __construct($id, $module, GetNetClickService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    public function actionValidate()
    {
        try {
            $requestToken = $this->request->get('_r');
            if (!$requestToken) return $this->redirect(\Yii::$app->params['getnet']['fail']);
            $this->service->processSubscription($requestToken);
            return $this->redirect(\Yii::$app->params['getnet']['success']);
        } catch (\Exception $e) {
            \Yii::error($e);
            return $this->redirect(\Yii::$app->params['getnet']['fail']);
        }
    }
}