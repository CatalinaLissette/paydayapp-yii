<?php


namespace app\services;


use app\enums\StateEnum;
use app\enums\StateOrderEnum;
use app\models\Order;
use app\models\ProviderHasCommerce;
use app\models\User;
use yii\db\Exception;
use yii\web\NotFoundHttpException;
use function Lambdish\Phunctional\map;

class CommerceService
{
    /**
     * @var User
     */
    private User $model;

    public function __construct()
    {
        $this->model = new User();
    }

    public function findProviders(int $commerce_id)
    {
        $results = ProviderHasCommerce::find()
            ->where([ProviderHasCommerce::tableName() . '.commerce_id' => $commerce_id])
            ->joinWith(['provider', 'commerce'])->asArray(true)->all();
        return map(function ($data) {
            return $this->reformatData($data);
        }, $results);
    }

    public function updateEnrollmentState(array $params): bool
    {
        $commerce_id = $params['commerce_id'];
        $provider_id = $params['provider_id'];
        $state = $params['state'];

        $relation = ProviderHasCommerce::findOne(['commerce_id' => $commerce_id, 'provider_id' => $provider_id]);
        if (!$relation) throw new NotFoundHttpException();
        ProviderHasCommerce::validateState($state);
        $relation->state = $state;
        $relation->credit = 0;
        return $relation->save();
    }

    public function enrollments(?int $status)
    {
        return ProviderHasCommerce::find()
            ->filterWhere([ProviderHasCommerce::tableName() . '.state' => $status])
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

    public function findProviderCommerce(int $commerce_id, int $provider_id)
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
        if ($enrolllements) {
            throw new Exception('no se puede eliminar porque hay enrolamientos');
        }
    }

    private function ensuredOrders(array $orders)
    {
        if ($orders) {
            throw new Exception('no se puede eliminar porque hay ordenes activas de pago');
        }
    }

    public function enroll($post)
    {
        $model = ProviderHasCommerce::createEnrollment($post);
        return $model;
    }

    private function reformatData($data)
    {
        return [
            'provider_id' => $data['provider_id'],
            'createdAt' => $data['created_at'],
            'updatedAt' => $data['updated_at'],
            'state' => $data['state'],
            'commerce_id' => $data['commerce_id'],
            'credit' => $data['credit'],
            'commerce' => [
                'id' => $data['commerce_id'],
                'createdAt' => $data['commerce']['createdAt'],
                'updatedAt' => $data['commerce']['updatedAt'],
                'state' => $data['commerce']['state'],
                'businessType' => $data['commerce']['businessType'],
                'user' => [
                    'id' => $data['commerce']['id'],
                    'commerce_id' => $data['commerce']['id'],
                    'provider_id' => null,
                    'email' => $data['commerce']['email'],
                    'state' => $data['commerce']['state'],
                    'createdAt' => $data['commerce']['createdAt'],
                    'updatedAt' => $data['commerce']['updatedAt'],
                    'rut' => $data['commerce']['rut'],
                    'name' => $data['commerce']['name'],
                    'businessName' => $data['commerce']['businessName'],
                    'address' => $data['commerce']['address'],
                    'supervisor' => $data['commerce']['supervisor'],
                    'phone' => $data['commerce']['phone'],
                    'uuid' => $data['commerce']['uuid'],
                    'commune_id' => $data['commerce']['commune_id']
                ],
                'provider' => [
                    'id' => $data['provider_id'],
                    'createdAt' => $data['provider']['createdAt'],
                    'updatedAt' => $data['provider']['updatedAt'],
                    'state' => $data['provider']['state'],
                    'user' => [
                        'id' => $data['provider']['id'],
                        'commerce_id' => null,
                        'provider_id' => $data['provider']['id'],
                        'email' => $data['provider']['email'],
                        'state' => $data['provider']['state'],
                        'createdAt' => $data['provider']['createdAt'],
                        'updatedAt' => $data['provider']['updatedAt'],
                        'rut' => $data['provider']['rut'],
                        'name' => $data['provider']['name'],
                        'businessName' => $data['provider']['businessName'],
                        'address' => $data['provider']['address'],
                        'supervisor' => $data['provider']['supervisor'],
                        'phone' => $data['provider']['phone'],
                        'uuid' => $data['provider']['uuid'],
                        'commune_id' => $data['provider']['commune_id']
                    ]
                ]
            ]
        ];
    }


}