<?php


namespace app\api\v1\controllers;


use app\models\User;
use app\services\UserService;
use yii\filters\Cors;
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
        $behaviors = parent::behaviors();
        $behaviors['cors'] = [
            'class' => Cors::class,
        ];

        return $behaviors;
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
