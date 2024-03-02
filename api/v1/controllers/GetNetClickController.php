<?php

namespace app\api\v1\controllers;

use app\models\KhipuAccount;
use app\services\GetNetClickService;
use DateInterval;
use DateTime;


class GetNetClickController extends SafeController
{
    public $modelClass = KhipuAccount::class;
    private $login = '42706f40bac8b72331210da246fa71c9';
    private $secretKey = 'mssSX27S6aH8nqfm';
    private GetNetClickService $getNetClickService;

    public function __construct(
        $id,
        $module,
        $config = [],
        GetNetClickService $getNetClickService
    )
    {
        parent::__construct($id, $module, $config);
        $this->getNetClickService = $getNetClickService;
    }


    public function actionCreateSubscription()
    {
        $data = $this->request->post();
        list($expirationDate, $auth) = $this->generateLogin($this->secretKey, $this->login);
        $data = [
            'auth' => $auth,
            'subscription' => [
                'reference' => $data['subscription'][0]['reference'],
                'description' => $data['subscription'][0]['description'],
            ],
            'expiration' => $expirationDate,
            'returnUrl' => $data['returnUrl'],
            'ipAddress' => $data['ipAddress'],
            'userAgent' => $data['userAgent'],
        ];
        return  [
            'auth' => $auth,
            'response' => $this->getNetClickService->createSuscription($data)
        ];

    }
    public function actionGetRequestInformation($request_id)
    {
        $data = $this->request->post();
        list($expirationDate, $auth) = $this->generateLogin($this->secretKey, $this->login);
        $data = [
            'auth' => $auth
        ];
        return  [
            'auth' => $auth,
            'response' => $this->getNetClickService->getRequestInformation($data,$request_id)
        ];

    }
    public function actionCollect()
    {
        $data = $this->request->post();
        $login = $data['auth']['login'];
        $secretKey = $data['auth']['secretKey'];
        $instrument = $data['instrument'];
        $payer = $data['payer'];
        $payment = $data['payment'];

        list($expirationDate, $auth) = $this->generateLogin($secretKey, $login);
        $data = [
            'auth' => $auth,
            'instrument' => $instrument,
            'payer' => $payer,
            'payment' => $payment,
        ];
        return  [
            'auth' => $auth,
            'response' => $this->getNetClickService->collect($data)
        ];

    }
    public function actionInvalidate()
    {
        $data = $this->request->post();
        $login = $data['auth']['login'];
        $secretKey = $data['auth']['secretKey'];
        $instrument = $data['instrument'];

        list($expirationDate, $auth) = $this->generateLogin($secretKey, $login);
        $data = [
            'auth' => $auth,
            'instrument' => $instrument
        ];
        return  [
            'response' => $this->getNetClickService->invalidate($data)
        ];

    }

    /**
     * @param $secretKey
     * @param $login
     * @return array
     * @throws \Random\RandomException
     */
    private function generateLogin($secretKey, $login): array
    {
        $nonce = random_int(0, PHP_INT_MAX);
        $nonceBase64 = base64_encode($nonce);
        $dateTime = new DateTime();
        $expiration = new DateTime();
        $dateTime->add(new DateInterval('PT4M'));
        $expiration->add(new DateInterval('PT10M'));
        $seed = $dateTime->format('c');
        $expirationDate = $expiration->format('c');
        $tranKeySum = $nonce . $seed . $secretKey;
        $sha256 = hash('sha256', $tranKeySum, true);
        $tranKey = base64_encode($sha256);
        $auth = [
            'login' => $login,
            'tranKey' => $tranKey,
            'nonce' => $nonceBase64,
            'seed' => $seed,
        ];
        return array($expirationDate, $auth);
    }

}