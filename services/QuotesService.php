<?php


namespace app\services;

class QuotesService
{
    private KhipuService $kiphuService;

    public function __construct(
        KhipuService $khipuService
    )
    {
        $this->kiphuService = $khipuService;
    }


    public function createPayment(
        int $amount,
        string $email,
        int $orderId,
        array $orderDetail
    )
    {
       // $result  = $this->kiphuService->createPayment('PAGO DE CUOTAS',$amount);
        $result  = $this->kiphuService->createPayment();
        print_r($result);
        return $result;
    }

}