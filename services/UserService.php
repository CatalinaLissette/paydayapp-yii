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
    private AuthService $authService;

    public function __construct(User $model, AuthService $authService)
    {
        $this->model = $model;
        $this->authService = $authService;
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
        $data['businessType'] = $data['commerce']['businessType'];
        unset($data['commerce']);
        User::createCommerce($data);
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
            $this->checkActualPasswordValidity($user->password_hash, $post['actualPassword']);
            $user->checkPasswordEq($post['password'], $post['rePassword']);
            $user->password_hash = $this->authService->generatePasswordHash($post['password']);
            $user->save(false);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    private function checkActualPasswordValidity($passwordHash, $passwordString): void
    {
        if (!$this->authService->validatePassword($passwordHash, $passwordString)) {
            throw new \Exception('passwords dont match');
        }
    }

    public function test()
    {
        $user = User::find()->one();
    }
}