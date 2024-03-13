<?php

namespace app\api\v1\controllers;

use app\models\KhipuAccount;
use app\services\GetNetClickService;
use DateInterval;
use DateTime;


class GetNetClickController extends SafeController
{
    public $modelClass = KhipuAccount::class;
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
        $user = \Yii::$app->user->identity;
        $redirectUrl = $this->getNetClickService->createSubscription(
            $user, $this->request->remoteIP, $this->request->userAgent
        );

        return $this->redirect($redirectUrl);
    }

    public function actionGetRequestInformation($request_id)
    {
        $data = $this->request->post();
        list($expirationDate, $auth) = $this->generateLogin($this->secretKey, $this->login);
        $data = [
            'auth' => $auth
        ];
        return [
            'auth' => $auth,
            'response' => $this->getNetClickService->getRequestInformation($data, $request_id)
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
        return [
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
        return [
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

        return array($expirationDate, $auth);
    }

}