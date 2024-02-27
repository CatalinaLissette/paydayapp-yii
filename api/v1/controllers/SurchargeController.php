<?php

namespace app\api\v1\controllers;

use app\models\Surcharge;
use app\services\SurchargeService;

class SurchargeController extends SafeController
{
    public $modelClass = Surcharge::class;

    private SurchargeService $surchargeService;

    public function __construct(
        $id,
        $module,
        $config = [],
        SurchargeService $surchargeService
    )
    {
        parent::__construct($id, $module, $config);
        $this->surchargeService = $surchargeService;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create'], $actions['view']);
        return $actions;
    }

    public function actionUpdate( string $id)
    {
        $post = $this->request->post();
        print_r($post['amount']);
        $this->surchargeService->updateSurcharge($id,$post['amount']);

    }

    public function actionView()
    {
        return $this->surchargeService->findSurcharge('1');
    }
}