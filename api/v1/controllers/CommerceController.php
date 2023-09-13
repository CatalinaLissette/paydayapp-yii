<?php


namespace app\api\v1\controllers;


use app\models\Commerce;
use app\models\ProviderHasCommerce;
use app\models\User;
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
}
