<?php


namespace app\services;

use app\enums\StateOrderEnum;
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
            if(!$result)
                throw new \Exception("no se ha encontrado asociacion de khipu para el proveedor");

            $notifyUrl = "https://payday.cl/v1/quotes/khipu/notification/{$result->reference_id}";


            $result = $this->kiphuService->createPayment($amount, $email, $subject,$notifyUrl,$orderId, $result);
            $paymentId = $result['payment_id'];
            if(!$paymentId)
                throw new \Exception("no se ha generado el paymentId");

            $this->setPaymentIdInOrderDetail($paymentId, $orderDetail, $orderId);


            $transaction->commit();

            return $result;
        } catch (\Exception $e) {

            $transaction->rollBack();
            throw $e;
        }
    }

    public function deletePayment(
        string $paymentId,
        int $providerId,
        int $orderId
    )
    {

            $result = $this->kiphuService->getKeysForKhipu($providerId);
            if(!$result)
                throw new \Exception("no se ha encontrado asociacion de khipu para el proveedor");

            $result = $this->kiphuService->deletePayment($paymentId, $result);
            $this->setPaymentState($orderId,$paymentId, StateOrderEnum::PENDING, false);
            return $result;
    }

    private function setPaymentIdInOrderDetail(string $paymentId, array $orderDetail, int $orderId)
    {
        foreach ($orderDetail as $detail) {
            $getOrderDetail = $this->quoteModel::findOne([
                'id' => $detail['id'],
                'order_id' => $orderId
            ]);

            if ($getOrderDetail) {
                $this->quoteModel::updateAll(
                    [
                        'paymentId' => $paymentId,
                        'state' => StateOrderEnum::PROCESSING
                    ],
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

    public function verifyPaymentQuotes(string $notificationToken,string $apiVersion,string $referenceId)
    {
        if($apiVersion !== '1.3'){
            throw new Exception('la version de api khipu es antigua');
        }

        $khipuAcount = $this->getKhipuAccountByReferenceId($referenceId);
        if($khipuAcount == null)
            throw new Exception('no se ha encontrado un cobrador valido');

        $payments = $this->kiphuService->getPayments($notificationToken,$khipuAcount->receiver_id, $khipuAcount->key);

        $transactionId = intval($payments['transaction_id']);

        $amountFromdb = $this->getAmountByOrderDetail($transactionId,$payments['payment_id']);

        if($payments['status'] == 'done' && intval($amountFromdb) == intval($payments['amount'])){
            $this->setPaymentState($transactionId,$payments['payment_id'], StateOrderEnum::PAYED, true);
        }else{
            $this->setPaymentState($transactionId,$payments['payment_id'], StateOrderEnum::PENDING, false);
            throw new Exception('el pago no ha sido verificado');
        }

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

    private function setPaymentState(int $id, string $payment_id, string $state,bool $opt)
    {
        $order = $this->orderModel::findOne($id);
        if (!$order)
            throw new Exception('no se encontro la orden');
        $quotes = $order->quotes;

        foreach ($quotes as $quote){
            if($opt && $quote['paymentId'] == $payment_id){
                $quote->state = $state;
                $quote->save();
            }else if(!$opt && $quote['paymentId'] == $payment_id){
                $quote->state = $state;
                $quote->paymentId = null;
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

    private function getKhipuAccountByReferenceId(string $referenceId)
    {
        return $this->kiphuService->getKeysForKhipuByReferenceId($referenceId);
    }

}