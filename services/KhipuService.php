<?php


namespace app\services;

use app\models\KhipuAccount;
use Khipu\Configuration;
use Khipu\ApiClient;
use Khipu\Client\PaymentsApi;


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



    public function createPayment(
        int $amount,
        string $email,
        int $providerId,
        string $subject,
        string $notifyUrl,
        int $transactionId
    )
    {

        $data = $this->getKeysForKhipu($providerId);
        $configuration = new Configuration();
        $configuration->setReceiverId($data['receiver_id']);
        $configuration->setSecret($data['key']);
        // $configuration->setDebug(true);

        $client = new ApiClient($configuration);
        $payments = new PaymentsApi($client);

      //  try {
            $opts = array(
                "payer_email" => $email,
                "notify_url" => $notifyUrl,
                "notify_api_version" => "1.3",
                "transaction_id" => $transactionId
              //  "return_url" => "http://mi-ecomerce.com/backend/return",
              //  "cancel_url" => "http://mi-ecomerce.com/backend/cancel",
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
//        } catch (\Khipu\ApiException $e) {
//            return $e->getResponseBody();
//        }
    //    return '';
    }

    public function getPayments(string $notificationToken)
    {

    }

    public function getReceiverById(int $receiverId)
    {
        return $this->model::findOne(['receiver_id' =>$receiverId]);

    }

}