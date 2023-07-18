<?php


namespace app\services;

use Khipu\Configuration;
use Khipu\ApiClient;
use Khipu\Client\PaymentsApi;


class KhipuService
{
    private $receiverId;
    private $secretKey;

    public function __construct($receiverId, $secretKey)
    {
        $this->receiverId = $receiverId;
        $this->secretKey = $secretKey;
    }

    public function createPayment()
    {
        $configuration = new Configuration();
        $configuration->setReceiverId($this->receiverId);
        $configuration->setSecret($this->secretKey);
        // $configuration->setDebug(true);

        $client = new ApiClient($configuration);
        $payments = new PaymentsApi($client);

      //  try {
            $opts = array(
                "transaction_id" => "MTI-100",
                "return_url" => "http://mi-ecomerce.com/backend/return",
                "cancel_url" => "http://mi-ecomerce.com/backend/cancel",
                "picture_url" => "http://mi-ecomerce.com/pictures/foto-producto.jpg",
                "notify_url" => "http://mi-ecomerce.com/backend/notify",
                "notify_api_version" => "1.3"
            );
            $response = $payments->paymentsPost(
                "Compra de prueba de la API", // Motivo de la compra
                "CLP", // Monedas disponibles CLP, USD, ARS, BOB
                100.0, // Monto. Puede contener ","
                $opts // Campos opcionales
            );

            return $response;
//        } catch (\Khipu\ApiException $e) {
//            return $e->getResponseBody();
//        }
    //    return '';
    }

}