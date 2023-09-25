<?php


namespace app\services;


use app\models\Provider;
use app\models\ProviderHasCommerce;
use yii\web\NotFoundHttpException;

class ProviderService
{
    /**
     * @var Provider
     */
    private Provider $model;

    public function __construct()
    {
        $this->model = new Provider();
    }

    public function findCommerces(int $provider_id)
    {
        $provider = $this->model::findOne($provider_id);
        if (!$provider) throw new NotFoundHttpException();
        return ProviderHasCommerce::find()
            ->where([ProviderHasCommerce::tableName().'.provider_id' => $provider_id])
            ->joinWith(['commerce', 'commerce.user', 'provider', 'provider.user'])
            ->asArray(true)->all();
    }
}
