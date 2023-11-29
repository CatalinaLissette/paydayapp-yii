<?php


namespace app\api\v1\controllers;


use app\models\Order;
use app\models\Quote;
use app\services\OrderService;
use Yii;
use yii\rest\ActiveController;

class OrderController extends SafeController
{
    public $modelClass = Order::class;

    private OrderService $orderService;

    public function __construct(
        $id,
        $module,
        $config = [],
        OrderService $orderService
    )
    {
        parent::__construct($id, $module, $config);
        $this->orderService = $orderService;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create'], $actions['view']);
        return $actions;
    }

    public function actionCreate()
    {
        $requestData = Yii::$app->request->getBodyParams();
        return $this->orderService->create($requestData);

    }

    public function actionView($order_id)
    {
        return $this->orderService->getQuotesByOrderId($order_id);
    }

    public function actionStatusQuotesPayment($payment_id){


        return $this->orderService->getOrderFromPaymentId($payment_id);
    }

    public function actionByCommerce($commerce_id){

        return $this->orderService->getOrderByCommerceId($commerce_id);
    }

    public function actionByProvider($provider_id){

        return $this->orderService->getOrderByProviderId($provider_id);
    }

}