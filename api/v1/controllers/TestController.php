<?php


namespace app\api\v1\controllers;


class TestController extends SafeController
{
    public $modelClass = "app\models\User";
    public function actionPrueba()
    {
        return "asdasd";
    }
}