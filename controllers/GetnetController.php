<?php

namespace app\controllers;

use app\models\User;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class GetnetController extends Controller
{
    public function actionValidate()
    {
        $uuid = $this->request->get('request');
        $user = User::findOne(['uuid' => $uuid]);
        if (!$user) throw new BadRequestHttpException('no no no!!');

        $requestId = $user->paymentInfos[0]->request_id;
        $info = $user->paymentInfos[0];
        //TODO: metodo collect
        $info->token = 'tokendetodofueok'; //$data['token']
        //TODO: save token
        $info->save(false);
        //TODO: caso contrario
        //throw new
        return $this->redirect("http://localhost:9000/#/getnet?result=success");
    }
}