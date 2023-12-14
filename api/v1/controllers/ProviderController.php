<?php


namespace app\api\v1\controllers;


use app\models\Provider;
use app\models\User;
use app\services\ProviderService;
use yii\rest\ActiveController;
use yii\web\Response;

class ProviderController extends SafeController
{
    public $modelClass = Provider::class;
    /**
     * @var ProviderService
     */
    private ProviderService $service;

    public function __construct($id, $module, ProviderService $service,$config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }



    public function actionCommerces(string $user_id)
    {
        $user = User::findOne(['uuid' => $user_id]);
        $this->response->format = Response::FORMAT_JSON;
        $commerces = $this->service->findCommerces($user->provider_id);
        return $commerces;
    }
}

