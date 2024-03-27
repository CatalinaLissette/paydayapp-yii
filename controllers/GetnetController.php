<?php

namespace app\controllers;

use app\services\GetNetClickService;
use app\services\PaymentInfoService;
use yii\web\Controller;

class GetnetController extends Controller
{
    private GetNetClickService $service;
    private PaymentInfoService $paymentService;

    public function __construct(
        $id,
        $module,
        GetNetClickService $service,
        PaymentInfoService $paymentService,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
        $this->paymentService = $paymentService;
    }

    public function actionValidate()
    {
        try {
            $requestToken = $this->request->get('_r');
            if (!$requestToken) return $this->redirect(\Yii::$app->params['getnet']['fail']);
            $user_id = $this->service->processSubscription($requestToken);
            $this->paymentService->disableOtherPaymentsMethodByUserId($user_id);
            return $this->redirect(\Yii::$app->params['getnet']['success']);
        } catch (\Exception $e) {
            \Yii::error($e);
            return $this->redirect(\Yii::$app->params['getnet']['fail']);
        }
    }
}