<?php

namespace app\services;

use app\models\Surcharge;
use yii\web\NotFoundHttpException;

class SurchargeService
{
    /**
     * @var Surcharge
     */
    public function __construct(
        Surcharge $surcharge
    )
    {
        $this->model = $surcharge;

    }

    public function updateSurcharge(int $id,int $amount)
    {
        $this->model::findOne($id);
        $this->model::updateAll([
            'amount' => $amount,
        ],[
            'id'=> $id
        ]);

    }

    public function findSurcharge(int $id)
    {
        $surcharge = $this->model::findOne($id);
        if (!$surcharge) throw new NotFoundHttpException();
        return $surcharge;
    }


}