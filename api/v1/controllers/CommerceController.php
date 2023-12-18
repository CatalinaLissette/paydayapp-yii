<?php


namespace app\api\v1\controllers;


use app\models\Commerce;
use app\models\ProviderHasCommerce;
use app\models\User;
use app\services\CommerceService;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
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
        return "";
    }

    public function actionEnrollments()
    {
        $status = $this->request->get('state');
        $this->response->format = Response::FORMAT_JSON;
        $enrollments = $this->commerceService->enrollments($status);
        return $enrollments;
    }

    public function actionProviders(string $user_id)
    {
        $user = User::findOne(['uuid' => $user_id]);
        if (!$user) throw new NotFoundHttpException("user not found");
        $this->response->format = Response::FORMAT_JSON;
        $providers = $this->commerceService->findProviders($user->commerce_id);
        return $providers;
    }

    public function actionUpdateEnrollment()
    {
        $this->response->format = Response::FORMAT_JSON;
        $this->commerceService->updateEnrollmentState(
            $this->request->getBodyParams()
        );
        $this->response->setStatusCode(201);
        return ['updated' => true];
    }

    public function actionUpdateCredit()
    {
        $this->response->format = Response::FORMAT_JSON;
        $this->commerceService->updateCredit(
            $this->request->getBodyParams()
        );
        $this->response->setStatusCode(201);
        return ['updated' => true];
    }

    public function actionProviderCommerce(string $user_id, string $provider_id)
    {
        $user = User::findOne(['uuid' => $user_id]);
        $this->response->format = Response::FORMAT_JSON;
        $providerCommerce = $this->commerceService->findProviderCommerce($user->commerce_id, $provider_id);
        return $providerCommerce;
    }
}
