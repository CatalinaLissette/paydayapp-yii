<?php


namespace app\api\v1\controllers;


use app\models\Commerce;
use app\models\ProviderHasCommerce;
use app\services\CommerceService;
use Yii;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class CommerceController extends SafeController
{
    public $modelClass = Commerce::class;
    /**
     * @var CommerceService
     */
    private CommerceService $commerceService;

    public function __construct($id, $module, CommerceService $commerceService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->commerceService = $commerceService;
    }

    public function actionEnroll()
    {
        $model = ProviderHasCommerce::enroll($this->request->post());
        $this->response->format = 'json';
        if ($model->save()) {
             $this->response->setStatusCode(201);
            return ['created' => true];
        }
        throw new BadRequestHttpException(Json::encode($model->errors));
    }

    public function actionProviders(string $commerce_id)
    {
        $this->response->format = Response::FORMAT_JSON;
        $providers = $this->commerceService->findProviders($commerce_id);
        return $providers;
    }
}