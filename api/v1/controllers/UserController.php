<?php


namespace app\api\v1\controllers;


use app\models\User;
use app\services\UserService;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;


class UserController extends ActiveController
{
    public $modelClass = User::class;
    /**
     * @var UserService
     */
    private UserService $userService;

    public function __construct($id, $module, UserService $userService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->userService = $userService;
    }

    public function behaviors()
    {
        return ArrayHelper::merge([
            'cors' => [
                'class' => Cors::class
            ],
        ], parent::behaviors());
    }


    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
        ];
    }


    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        return $actions;
    }

    public function actionCreate()
    {
        $post = $this->request->post();
        $this->userService->createUser($post);
        $this->response->setStatusCode(201);
        $this->response->content = '';
        return;
    }
}
