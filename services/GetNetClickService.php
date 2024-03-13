<?php

namespace app\services;

use app\models\PaymentInfo;
use app\models\User;
use DateInterval;
use DateTime;

class GetNetClickService
{
    private HttpService $httpService;

    public function __construct(
        HttpService $httpService
    )
    {
        $this->httpService = $httpService;
    }

    public function createSubscription(User $user, string $ip, string $userAgent): string
    {
        $data = [
            'auth' => $this->generateLogin(),
            'subscription' => [
                'reference' => "user-$user->id",
                'description' => "1",
            ],
            'expiration' => $this->calcKeyTimeSeed('PT10M'),
            'returnUrl' => "http://localhost:8030/getnet/validate?request={$user->uuid}",
            'ipAddress' => $ip,
            'userAgent' => $userAgent,
        ];

        //var_dump($data);

        $response = $this->httpService->request(
            'POST',
            'https://checkout.test.getnet.cl/api/session/',
            $data
        );
        $data = $response->data;
        $this->persistRequestId($user, $data['requestId']);
        print_r($data['processUrl']);
        return $data['processUrl'];
    }

    public function getRequestInformation(array $data, string $requestId)
    {

        $response = $this->httpService->request(
            'POST',
            "https://checkout.test.getnet.cl/api/session/" . $requestId,
            $data
        );
        return $response->data;
    }

    public function collect(array $data)
    {

        $response = $this->httpService->request(
            'POST',
            "https://checkout.test.getnet.cl/api/collect",
            $data
        );
        return $response->data;
    }

    public function invalidate(array $data)
    {

        $response = $this->httpService->request(
            'POST',
            "https://checkout.test.getnet.cl/api/instrument/invalidate",
            $data
        );
        return $response->data;
    }

    private function generateLogin()
    {
        $nonce = random_int(0, PHP_INT_MAX);
        $nonceBase64 = base64_encode($nonce);
        $seed = $this->calcKeyTimeSeed('PT4M');
        $tranKeySum = $nonce . $seed . \Yii::$app->params['getnet']['secretKey'];
        $sha256 = hash('sha256', $tranKeySum, true);
        $tranKey = base64_encode($sha256);
        return [
            'login' => \Yii::$app->params['getnet']['login'],
            'tranKey' => $tranKey,
            'nonce' => $nonceBase64,
            'seed' => $seed,
        ];
    }

    public function calcKeyTimeSeed(string $delta): string
    {
        $dateTime = new DateTime();
        $dateTime->add(new DateInterval($delta));
        return $dateTime->format('c');
    }

    private function persistRequestId(User $user, $requestId)
    {
        PaymentInfo::createPayment($user, $requestId);
    }
}