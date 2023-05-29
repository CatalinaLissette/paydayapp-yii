<?php


namespace app\api\v1\controllers;


use app\models\Quote;
use yii\rest\ActiveController;

class QuoteController extends ActiveController
{
    public $modelClass = Quote::class;

}