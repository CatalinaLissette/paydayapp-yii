<?php


namespace app\api\v1\controllers;


use app\models\Offer;
use app\services\OfferService;
use Yii;
use yii\filters\Cors;
use yii\rest\ActiveController;

class OfferController extends ActiveController
{
    public $modelClass = Offer::class;
    private OfferService $offerService;

    public function __construct(
        $id,
        $module,
        $config = [],
        OfferService $offerService
    )
    {
        parent::__construct($id, $module, $config);
        $this->offerService = $offerService;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['cors'] = [
            'class' => Cors::class,
        ];

        return $behaviors;
    }

    public function actionCreate()
    {
        $requestData = Yii::$app->request->getBodyParams();
        return $this->offerService->create($requestData);
    }

    public function actionUpdate($id)
    {
        $requestData = Yii::$app->request->getBodyParams();
        return $this->offerService->update($id,$requestData);
    }

    public function actionByProvider($provider_id){
        return $this->offerService->getOffersByProviderId($provider_id);
    }

}