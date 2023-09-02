<?php


namespace app\api\v1\controllers;


use app\models\User;
use app\services\UserService;

class UserController extends SafeController
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
