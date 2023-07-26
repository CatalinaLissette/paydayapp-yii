<?php


namespace app\services;

use app\models\Order;
use app\models\Quote;
use app\models\User;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

class QuotesService
{
    private KhipuService $kiphuService;
    /**
     * @var Order
     */
    private Order $orderModel;
    /**
     * @var Quote
     */
    private Quote $quoteModel;
    /**
     * @var User
     */
    private User $userModel;

    public function __construct(
        KhipuService $khipuService,
        Order $orderModel,
        Quote $quoteModel,
        User $userModel
    )
    {
        $this->kiphuService = $khipuService;
        $this->orderModel = $orderModel;
        $this->quoteModel = $quoteModel;
        $this->userModel = $userModel;
    }


    public function createPayment(
        int $amount,
        string $email,
        int $orderId,
        array $orderDetail,
        int $providerId,
        string $subject
    )
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $result = $this->kiphuService->getKeysForKhipu($providerId);

            $notifyUrl = "https://payday.cl/v1/quotes/khipu/notification/${$result['reference_id']}";


            $result = $this->kiphuService->createPayment($amount, $email, $providerId, $subject,$notifyUrl,$orderId,$result);
            //VALIDAR QUE PAYMENT_ID VENGA
            $paymentId = $result['payment_id'];


            $this->setPaymentIdInOrderDetail($paymentId, $orderDetail, $orderId);


            $transaction->commit();

            return $result;
        } catch (\Exception $e) {

            $transaction->rollBack();
            throw $e;
        }
    }

    private function setPaymentIdInOrderDetail(string $paymentId, array $orderDetail, int $orderId)
    {
        foreach ($orderDetail as $detail) {
            $getOrderDetail = $this->quoteModel::findOne([
                'id' => $detail['id'],
                'order_id' => $orderId
            ]);

            if ($getOrderDetail) {
                // Actualizar el campo 'payment_id' en la tabla 'quote'
                $this->quoteModel::updateAll(
                    ['paymentId' => $paymentId],
                    [
                        'id' => $getOrderDetail['id'],
                        'order_id' => $orderId,
                    ]
                );
                continue;
            }
            throw new \Exception("Detalle de pedido no encontrado para el ID: {$detail['id']}");
        }
    }

    public function verifyPaymentQuotes(string $notificationToken, string $apiVersion)
    {
        if($apiVersion !== '1.3'){
            throw new Exception('la version de api khipu es antigua');
        }
        $payments = $this->kiphuService->getPayments($notificationToken);

        $transactionId = intval($payments['transaction_id']);

        $receiver = $this->kiphuService->getReceiverById($payments['receiver_id']);
        if($receiver == null)
            throw new Exception('no se ha verificado el cobrador id');



        if($payments['status'] != 'done')
            throw new Exception('el pago no ha sido verificado');

        $amountFromdb = $this->getAmountByOrderDetail($transactionId,$payments['payment_id']);

        if(intval($amountFromdb) !== intval($payments['amount']))
            throw new Exception('los montos de pago no coinciden');

        $this->setPaymentSuccessfull($transactionId,$payments['payment_id']);

        $email = $this->getEmailByTransactionId($transactionId);

        return $email;


        //TODO OBTENER EMAIL POR TRANSACCIONID

    }

    private function getAmountByOrderDetail(int $id,string $payment_id)
    {
        $quotes = $this->findQuotesOrderById($id);
        $amountSum = 0;
        foreach ($quotes as $quote){
            if($quote['paymentId'] == $payment_id)
                $amountSum += $quote['quoteAmount'];
        }
        return $amountSum;

    }

    private function setPaymentSuccessfull(int $id, string $payment_id)
    {
        $order = $this->orderModel::findOne($id);
        if (!$order)
            throw new Exception('no se encontro la orden');
        $quotes = $order->quotes;

        foreach ($quotes as $quote){
            if($quote['paymentId'] == $payment_id){
                $quote->state = 3;
                $quote->save();
            }

        }

    }

    /**
     * @param int $id
     * @return array|array[]|object|object[]|string|string[]
     * @throws Exception
     */
    private function findQuotesOrderById(int $id)
    {
        $order = $this->orderModel::findOne($id);
        if (!$order) throw new Exception('no se encontro la orden');
        return ArrayHelper::toArray($order->quotes);
    }

    private function getEmailByTransactionId(int $transactionId)
    {
        $order = $this->orderModel::find()
            ->where(['id' => $transactionId])
            ->with('provider.users')
            ->one();

        if ($order) {
            $provider = $order->provider;
            $user = $this->userModel::findOne(['provider_id' => $provider->id]);
            return $user->email;
        }
        throw new Exception('No se encontró la orden');

    }

}