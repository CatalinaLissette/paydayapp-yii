<?php


namespace app\api\v1\controllers;


use app\models\Quote;
use app\services\QuotesService;
use yii\filters\Cors;
use yii\rest\ActiveController;

class QuoteController extends SafeController
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
    public function actionDeletePayment()
    {
        $post = $this->request->post();
        return $this->quotesService->deletePayment(
            $post['paymentId'],
            $post['providerId'],
            $post['orderId'],
        );

    }

}