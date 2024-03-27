<?php

namespace app\services;

use app\models\PaymentInfo;
use app\models\User;
use DateInterval;
use DateTime;
use yii\helpers\Json;

class GetNetClickService
{
    const TOKEN_KEYWORD = 'token';
    private HttpService $httpService;

    public function __construct(
        HttpService $httpService
    )
    {
        $this->httpService = $httpService;
    }

    public function createSubscription(User $user, string $ip, string $userAgent): string
    {
        $returnUrl = \Yii::$app->params['getnet']['returnUrl'];
        $requestToken = \Yii::$app->security->generateRandomString(32);
        $data = [
            'auth' => $this->generateLogin(),
            'subscription' => [
                'reference' => "$user->businessName",
                'description' => "1",
            ],
            'expiration' => $this->calcKeyTimeSeed('PT10M'),
            'returnUrl' => "$returnUrl?_r=$requestToken",
            'ipAddress' => $ip,
            'userAgent' => $userAgent,
        ];

        $response = $this->httpService->request(
            'POST',
            'https://checkout.test.getnet.cl/api/session/',
            $data
        );
        $data = $response->data;
        $this->persistRequestId($user, $data['requestId'], $requestToken);
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

    private function persistRequestId(User $user, int $requestId, string $requestToken)
    {
        PaymentInfo::createPayment($user, $requestId, $requestToken);
    }

    public function processSubscription(string $requestToken)
    {
        $info = PaymentInfo::findRequest($requestToken);
        $info->setToken($this->collectInfo($info->request_id));
        PaymentInfo::disableOther($info->user_id,$requestToken);
        $info->clearData();
        $info->save();
    }

    private function collectInfo(string $requestId): string
    {
        $response = $this->httpService->request('POST', "https://checkout.test.getnet.cl/api/session/$requestId", ['auth' => $this->generateLogin()]);
        if ($response->statusCode !== '200') throw new \Exception('error al tratar de obtener información de subscripción');
        return $this->tokenFromDataExtractor($response->data);
    }

    private function tokenFromDataExtractor(array $data): string
    {
        $instruments = $data['subscription']['instrument'];
        foreach ($instruments as $instrument) {
            if ($instrument['keyword'] === self::TOKEN_KEYWORD) {
                return $instrument['value'];
            }
        }
        throw new \Exception('token no encontrado');
    }

    public function generatePay(?User $user,array $params)
    {
        print_r($user);
        $token = '';
        $amount = $params['amount'];
        $reference = $params['reference'];
        $data = [
            'auth' => $this->generateLogin(),
            'instrument' => [
                'token' => [
                    'token' => $token
                ],
                'locale' => 'es_CL'
            ],
            'payer' =>[
                'document' =>  '11.222.333-9',
                'documentType' => 'CLRUT',
                'name' => 'Prueba aliado',
                'surname' => 'JA',
                'email' => 'rojaixz@gmail.com'
                ],
            'payment' =>[
                'reference' => $reference,
                'description' => 'Pago de cuota',
                'amount' => [
                    'currency' => 'CLP',
                    'total' => $amount,
                ],
            ],
        ];

    }
}