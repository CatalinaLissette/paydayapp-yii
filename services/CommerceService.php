<?php


namespace app\services;


use app\enums\StateEnum;
use app\enums\StateOrderEnum;
use app\models\Commerce;
use app\models\Order;
use app\models\Provider;
use app\models\ProviderHasCommerce;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
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
        $relation->credit = 0;
        return $relation->save();
    }

    public function enrollments(?int $status)
    {
        return ProviderHasCommerce::find()
            ->filterWhere([ProviderHasCommerce::tableName().'.state' => $status])
            ->joinWith(['commerce', 'commerce.user', 'provider', 'provider.user'])->asArray(true)->all();
    }

    public function updateCredit(array $params): bool
    {
        $commerce_id = $params['commerce_id'];
        $provider_id = $params['provider_id'];
        $credit = $params['credit'];

        $relation = ProviderHasCommerce::findOne(['commerce_id' => $commerce_id, 'provider_id' => $provider_id]);
        if (!$relation) throw new NotFoundHttpException();
        $relation->credit = $credit;
        $relation->state = StateEnum::ACTIVE;
        return $relation->save();
    }

    public function findProviderCommerce( int $commerce_id, int $provider_id)
    {
        $commerce = $this->model::findOne($commerce_id);
        if (!$commerce) throw new NotFoundHttpException();
        return ProviderHasCommerce::findOne(['commerce_id' => $commerce_id, 'provider_id' => $provider_id]);
    }

    public function disable($commerce_id)
    {
        $enrolllements = ProviderHasCommerce::findOne(['commerce_id' => $commerce_id]);
        $orders = Order::findAll(['commerce_id' => $commerce_id, 'state' => StateOrderEnum::PENDING]);
        $this->ensuredOrders($orders);
        $commerce = $this->model::findOne($commerce_id);
        $this->ensuredEnrollements($enrolllements);
        $commerce->state = StateEnum::DISABLED;
        return $commerce->update();
    }

    private function ensuredEnrollements(?ProviderHasCommerce $enrolllements)
    {
        if($enrolllements){
            throw new Exception('no se puede eliminar porque hay enrolamientos');
        }
    }

    private function ensuredOrders(array $orders)
    {
        if($orders){
            throw new Exception('no se puede eliminar porque hay ordenes activas de pago');
        }
    }


}