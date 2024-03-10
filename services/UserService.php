<?php

namespace app\services;

use app\models\Commerce;
use app\models\Provider;
use app\models\User;
use yii\db\ActiveQuery;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

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
        if (isset($data['provider'])) {
            $this->createProvider($data);
        } else {
            $this->createCommerce($data);
        }
    }

    private function createProvider(array $data)
    {
        User::createProvider($data['user']);
    }

    private function createCommerce(array $data)
    {
        $commerce = new Commerce($data['commerce']);
        if (!$commerce->save()) {
            throw new \Exception(Json::encode($commerce->errors));
        }
        unset($data['commerce']);
        $user = new User($data);
        $user->commerce_id = $commerce->id;
        if (!$user->save()) {
            $commerce->delete();
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

    public function changePassword(int $id, array $post)
    {
        try {
            $user = $this->model::findOne($id);
            if (!$user) throw new BadRequestHttpException('Error al cambiar contraseña');
            $user->password = $post['password'];
            $user->rePassword = $post['rePassword'];
            $user->validatePasswordEq();
            $user->save(false);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    public function test()
    {
        $user = User::find()->one();
    }
}