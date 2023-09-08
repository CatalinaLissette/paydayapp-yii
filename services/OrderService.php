<?php


namespace app\services;


use app\models\Order;
use app\models\Quote;
use app\enums\StateOrderEnum;
use app\models\User;
use Yii;
use yii\console\Exception;

class OrderService
{
    /**
     * @var Order
     */
    private Order $model;
    /**
     * @var EmailService
     */
    private EmailService $emailService;

    public function __construct(
        Order $model,
        EmailService $emailService
    )
    {
        $this->model = $model;
        $this->emailService = $emailService;

    }

    public function create(array $requestData)
    {

        $this->model->load(['Order' => $requestData]);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$this->model->save()) {
                throw new \Exception('Error al guardar la orden.');
            }

            foreach ($requestData['quotes'] as $quoteData) {
                $quote = new Quote();
                $quote->load(['Quote' => $quoteData]);
                $quote->order_id = $this->model->id;
                $quote->state = 1;

                if (!$quote->save()) {
                    throw new \Exception('Error al guardar la glosa.');
                }
            }

            $transaction->commit();


            // ENVIAR EMAIL A COMERCIO
            //OBTENER EMAIL DE COMERCIO
//            $this->emailService->sendEmail(
//                'rojaixz@hotmail.com',
//                'GENERACION DE NOTA DE VENTA',
//                'Se ha generado una nota de venta exitosamente'
//            );

            return ['status' => 'success', 'message' => 'Datos guardados correctamente.'];
        } catch (\Exception $e) {
            $transaction->rollBack();

            return ['status' => 'error', 'message' => $e->getMessage()];
        }

    }

    public function getOrderFromPaymentId($paymentId)
    {

        $orderDetail = Quote::find()
            ->where(['paymentId' => $paymentId])
            ->all();


        if (empty($orderDetail)) {
            throw new Exception("No se encontraron cuotas asociadas al ID.");
        }

        $totalPayment = count($orderDetail);
        $verifyCount = 0;

        foreach ($orderDetail as $item) {
            if ($item->state === StateOrderEnum::PAYED) {
                $verifyCount++;
            }
        }

        if ($totalPayment !== $verifyCount) {
            throw new Exception("la quota se encuentra pendiente de pago");
        }

        return [
            "message" =>"la cuota fue pagada con exito"
        ];
    }


    public function getOrderByCommerceId(string $commerce_id)
    {
        $user = User::findOne([
           'uuid' => $commerce_id
        ]);

        return  Order::find()
            ->where(['commerce_id' => $user->commerce_id])
            ->all();

    }
    public function getOrderByProviderId(string $provider_id)
    {
        $user = User::findOne([
            'uuid' => $provider_id
        ]);
        return  Order::find()
            ->where(['provider_id' => $user->provider_id])
            ->all();

    }

    public function getQuotesByOrderId($order_id)
    {
        return Order::findOne($order_id);
    }
}