<?php


namespace app\api\v1\controllers;


use app\models\KhipuAccount;
use yii\rest\ActiveController;

class KhipuAccountController extends ActiveController
{
    public $modelClass = KhipuAccount::class;
}