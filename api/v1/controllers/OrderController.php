<?php


namespace app\api\v1\controllers;


use app\models\Order;
use app\models\Quote;
use app\services\OrderService;
use Yii;
use yii\rest\ActiveController;

class OrderController extends ActiveController
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
        $order =  new $this->modelClass;

        if ($order !== null) {
            return $order;
        } else {
            throw new \yii\web\NotFoundHttpException("Order with ID $order_id not found.");
        }
    }

}