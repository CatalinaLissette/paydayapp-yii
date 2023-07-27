<?php


namespace app\api\v1\controllers;


use app\models\Quote;
use app\services\QuotesService;
use yii\rest\ActiveController;

class QuoteController extends ActiveController
{
    public $modelClass = Quote::class;

    private QuotesService $quotesService;

    public function __construct(
        $id,
        $module,
        $config = [],
        QuotesService $quotesService
    )
    {
        parent::__construct($id, $module, $config);
        $this->quotesService = $quotesService;
    }

    public function actionCreatePayment()
    {
        $post = $this->request->post();
        return $this->quotesService->createPayment(
            $post['amount'],
            $post['email'],
            $post['orderId'],
            $post['orderDetail'],
            $post['providerId'],
            $post['subject'],
        );

    }

    public function actionNotification($reference_id)
    {
        $post = $this->request->post();
        \Yii::debug($post);

        $email = $this->quotesService->verifyPaymentQuotes($post['notification_token'],$post['api_version']);

        //TODO:ENVIAR EMAIL

        return $reference_id;
    }

}