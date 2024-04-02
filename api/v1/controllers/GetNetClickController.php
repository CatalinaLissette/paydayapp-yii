<?php

namespace app\api\v1\controllers;

use app\models\KhipuAccount;
use app\services\GetNetClickService;
use app\services\QuotesService;
use DateInterval;
use DateTime;
use yii\db\Exception;


class GetNetClickController extends SafeController
{
    public $modelClass = KhipuAccount::class;
    private GetNetClickService $getNetClickService;
    private QuotesService $quotesService;

    public function __construct(
        $id,
        $module,
        $config = [],
        GetNetClickService $getNetClickService,
        QuotesService $quotesService
    )
    {
        parent::__construct($id, $module, $config);
        $this->getNetClickService = $getNetClickService;
        $this->quotesService = $quotesService;

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

        return [
            'response' => $this->getNetClickService->getRequestInformation( $request_id)
        ];

    }

    public function actionCollect()
    {
        $user = \Yii::$app->user->identity;
        $data = $this->request->post();
        $this->quotesService->validatePayment($data['orderDetail']);
        $response = $this->getNetClickService->generatePay($user,$data);
        $resp = $this->getNetClickService->getRequestInformation($response['requestId']);
        $this->quotesService->setPaymentByGetNet($data,$resp['requestId']);
        return $resp['payment'][0]['status'];

    }

    public function actionInvalidate()
    {
        $user = \Yii::$app->user->identity;
        return [
            'response' => $this->getNetClickService->invalidate($user)
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