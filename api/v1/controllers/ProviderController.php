<?php


namespace app\api\v1\controllers;


use app\models\Provider;
use app\models\User;
use app\services\ProviderService;
use yii\db\Expression;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ProviderController extends SafeController
{
    public $modelClass = Provider::class;
    /**
     * @var ProviderService
     */
    private ProviderService $service;

    public function __construct($id, $module, ProviderService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }


    public function actions()
    {
        $actions = parent::actions();
        unset($actions['view'], $actions['update']);
        return $actions;
    }

    public function actionView(string $uuid)
    {
        return $this->service->findByUuid($uuid);
    }

    public function actionUpdate(string $uuid)
    {
        $user = User::findOne(['uuid' => $uuid]);
        if (!$user) throw new NotFoundHttpException();
        if ($user->load(['User' => $this->request->post('user')])) {
            $user->save();
            $user->provider->updatedAt = new Expression('NOW()');
            return $user->provider->save();
        }
        throw new BadRequestHttpException();

    }

    public function actionCommerces(string $user_id)
    {
        $user = User::findOne(['uuid' => $user_id]);
        $this->response->format = Response::FORMAT_JSON;
        $commerces = $this->service->findCommerces($user->provider_id);
        return $commerces;
    }
}

