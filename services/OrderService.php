<?php


namespace app\services;


use app\models\Order;
use app\models\Quote;
use Yii;

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
}