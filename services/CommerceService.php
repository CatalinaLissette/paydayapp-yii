<?php


namespace app\services;


use app\models\Commerce;
use app\models\Provider;
use app\models\ProviderHasCommerce;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class CommerceService
{
    /**
     * @var Commerce
     */
    private Commerce $model;

    public function __construct()
    {
        $this->model = new Commerce();
    }

    public function findProviders(int $commerce_id)
    {
        $commerce = $this->model::findOne($commerce_id);
        if (!$commerce) throw new NotFoundHttpException();
        return ProviderHasCommerce::find()
            ->where([ProviderHasCommerce::tableName() . '.commerce_id' => $commerce_id])
            ->joinWith(['commerce', 'commerce.user', 'provider', 'provider.user'])->asArray(true)->all();
    }

    public function updateEnrollmentState(array $params): bool
    {
        $commerce_id = $params['commerce_id'];
        $provider_id = $params['provider_id'];
        $state = $params['state'];

        $relation = ProviderHasCommerce::findOne(['commerce_id' => $commerce_id, 'provider_id' => $provider_id]);
        if (!$relation) throw new NotFoundHttpException();
        ProviderHasCommerce::validateState($state);
        $relation->state =  $state;
        return $relation->save();
    }

    public function enrollments(?int $status)
    {
        return ProviderHasCommerce::find()
            ->filterWhere([ProviderHasCommerce::tableName().'.state' => $status])
            ->joinWith(['commerce', 'commerce.user', 'provider', 'provider.user'])->asArray(true)->all();
    }
}