<?php
namespace app\services;

use app\models\Commerce;
use app\models\Provider;
use app\models\User;
use yii\helpers\Json;

class UserService
{
    /**
     * @var User
     */
    private User $model;

    public function __construct(User $model)
    {

        $this->model = $model;
    }

    public function createUser(array $data)
    {
        if ($data['provider']) {
            $this->createProvider($data);
        } else {
            $this->createCommerce($data);
        }
    }

    private function createProvider(array $data)
    {
        $provider = new Provider($data['provider']);
        if (!$provider->save()) {
            throw new \Exception(Json::encode($provider->errors));
        }
        unset($data['provider']);
        $user = new User($data);
        $user->provider_id = $provider->id;
        if (!$user->save()) {
            throw new \Exception(Json::encode($user->errors));
        }
    }

    private function createCommerce(array $data)
    {
        $commerce = new Commerce($data['commerce']);
        if (!$commerce->save()) {
            throw new \Exception(Json::encode($commerce->errors));
        }
        unset($data['commerce']);
        $user = new User($data);
        $user->provider_id = $commerce->id;
        if (!$user->save()) {
            throw new \Exception(Json::encode($user->errors));
        }
    }

    public function findById(int $id): User
    {
        return $this->model::findOne($id);
    }

    public function findByUsername(string $username): ?User
    {
        return $this->model::findOne(['email' => $username, 'state' => User::STATUS_ACTIVE]);
    }

    public function findByUuid(int $user_id): ?User
    {
        return $this->model::findOne(['uuid' => $user_id, 'state' => User::STATUS_ACTIVE]);
    }
}