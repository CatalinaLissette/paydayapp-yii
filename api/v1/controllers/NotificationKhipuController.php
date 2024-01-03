<?php


namespace app\api\v1\controllers;


use app\models\Quote;
use app\services\QuotesService;
use yii\rest\ActiveController;

class NotificationKhipuController extends ActiveController
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

    public function actionNotification($reference_id)
    {
        $post = $this->request->post();

        $this->quotesService->verifyPaymentQuotes($post['notification_token'],$post['api_version'],$reference_id);

        return [];
    }
}