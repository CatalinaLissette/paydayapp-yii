<?php


namespace app\services;

use app\models\KhipuAccount;
use DateInterval;
use DateTime;
use Khipu\ApiException;
use Khipu\Configuration;
use Khipu\ApiClient;
use Khipu\Client\PaymentsApi;
use yii\db\Exception;


class KhipuService
{
    /**
     * @var KhipuAccount
     */
    private KhipuAccount $model;

    public function __construct(
        KhipuAccount $model
    )
    {
        $this->model = $model;
    }

    public function getKeysForKhipu(int $providerId){
        return $this->model::findOne(['provider_id' => $providerId]);
    }

    public function getKeysForKhipuByReferenceId(string $referenceId){
        return $this->model::findOne(['reference_id' => $referenceId]);
    }


    public function createPayment(
        int $amount,
        string $email,
        string $subject,
        string $notifyUrl,
        int $transactionId,
        KhipuAccount $khipuAccount,
        string $cancelUrl
    )
    {

            $configuration = new Configuration();
            $configuration->setReceiverId($khipuAccount->receiver_id);
            $configuration->setSecret($khipuAccount->key);

            $client = new ApiClient($configuration);
            $payments = new PaymentsApi($client);
            $expires_date = new DateTime();
            $expires_date->add(new DateInterval('PT5M'));
            $opts = array(
                "payer_email" => $email,
                "notify_url" => $notifyUrl,
                "notify_api_version" => "1.3",
                "transaction_id" => $transactionId,
                "expires_date" => $expires_date,
              //  "return_url" => "http://mi-ecomerce.com/backend/return",
                "cancel_url" => $cancelUrl,
              //  "picture_url" => "http://mi-ecomerce.com/pictures/foto-producto.jpg",

            );
            $response = $payments->paymentsPost(
                $subject, // Motivo de la compra
                "CLP", // Monedas disponibles CLP, USD, ARS, BOB
                $amount,
                $opts
            );

            return [
                'payment_id' => $response['payment_id'],
                'payment_url' => $response['payment_url'],
                'simplified_transfer_url' => $response['simplified_transfer_url'],
                'transfer_url' => $response['transfer_url'],
                'app_url' => $response['app_url'],
            ];
    }

    public function deletePayment(
        string $paymentId,
        KhipuAccount $khipuAccount
    )
    {

        $configuration = new Configuration();
        $configuration->setReceiverId($khipuAccount->receiver_id);
        $configuration->setSecret($khipuAccount->key);

        $client = new ApiClient($configuration);
        $payments = new PaymentsApi($client);

        return $payments->paymentsIdDelete($paymentId);
    }

    public function getPayments(string $notificationToken, int $receiverId, string $key )
    {
        try {
            $configuration = new Configuration();
            $client = new ApiClient($configuration);
            $configuration->setReceiverId($receiverId);
            $configuration->setSecret($key);
            $payments = new PaymentsApi($client);
            return $payments->paymentsGet($notificationToken);
        } catch (ApiException $e) {
            throw new \yii\console\Exception($e);
        }
    }

    public function getReceiverById(int $receiverId)
    {
        return $this->model::findOne(['receiver_id' =>$receiverId]);

    }

}