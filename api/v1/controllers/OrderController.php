<?php


namespace app\api\v1\controllers;


use app\models\Order;
use app\models\Quote;
use Yii;
use yii\rest\ActiveController;

class OrderController extends ActiveController
{
    public $modelClass = Order::class;

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create'], $actions['view']);
        return $actions;
    }

    public function actionCreate()
    {

        $requestData = Yii::$app->request->getBodyParams();
        $order = new Order();
        $order->load(['Order' => $requestData]);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$order->save()) {
                throw new \Exception('Error al guardar la orden.');
            }

            foreach ($requestData['quotes'] as $quoteData) {
                $quote = new Quote();
                $quote->load(['Quote' => $quoteData]);
                $quote->order_id = $order->id;
                $quote->state = 1;

                if (!$quote->save()) {
                    throw new \Exception('Error al guardar la glosa.');
                }
            }

            $transaction->commit();

            return ['status' => 'success', 'message' => 'Datos guardados correctamente.'];
        } catch (\Exception $e) {
            $transaction->rollBack();

            return ['status' => 'error', 'message' => $e->getMessage()];
        }
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