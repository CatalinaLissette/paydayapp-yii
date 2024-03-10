<?php

namespace app\services;

use app\models\RefreshToken;
use app\models\User;
use Yii;
use yii\db\Expression;
use yii\web\Cookie;
use yii\web\ServerErrorHttpException;

final class AuthService
{
    public function generateAuthToken(User $user)
    {

        $jwt = Yii::$app->jwt;
        $signer = $jwt->getSigner('HS256');

        $key = $jwt->getKey();
        $time = time();

        $jwtParams = Yii::$app->params['jwt'];
        return $jwt->getBuilder()
            ->issuedBy($jwtParams['issuer'])
            ->permittedFor($jwtParams['audience'])
            ->issuedAt($time)
            ->expiresAt($time + $jwtParams['expire'])
            ->withClaim('uid', $user->uuid)
            ->withClaim('type', $user->getUserType())
            ->withClaim('userName', $user->name)
            ->getToken($signer, $key);
    }

    public function generateRefreshToken(User $user, string $ip, string $userAgent): void
    {
//        $refreshToken = Yii::$app->security->generateRandomString(200);
//        $userToken = $this->saveNewToken($user, $refreshToken, $ip, $userAgent);
//        if (!$userToken) {
//            throw new ServerErrorHttpException('error');
//        }
//        Yii::$app->response->cookies->add(new Cookie([
//            'name' => 'refresh-token',
//            'value' => $refreshToken,
//            'httpOnly' => true,
//            'sameSite' => 'none',
//            'secure' => true,
//            'path' => '/api/v1/auth/refresh-token'
//        ]));
        //TODO: generate and save refresh token

    }

    public function validatePassword(string $hash, string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $hash);
    }

    private function saveNewToken(
        User   $user,
        string $refreshToken,
        string $ip,
        string $user_agent
    ): ?RefreshToken
    {
        $token = new RefreshToken([
            'user_id' => $user->uuid,
            'token' => $refreshToken,
            'ip' => $ip,
            'user_agent' => $user_agent,
            'created_at' => new Expression('NOW()')
        ]);

        if (!$token->save()) {
            Yii::error($token->errors);
            throw new \Exception('error generating refresh token');
        }
        return $token;
    }

    public function findRefreshToken(mixed $refreshToken): ?RefreshToken
    {
        return RefreshToken::findOne(['token' => $refreshToken]);
    }
}