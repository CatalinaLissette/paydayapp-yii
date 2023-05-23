<?php


namespace app\api\v1\controllers;


use app\models\User;
use app\services\AuthService;
use app\services\UserService;
use Yii;
use yii\rest\Controller;
use yii\rest\OptionsAction;
use yii\web\HttpException;
use yii\web\UnauthorizedHttpException;

class AuthController extends Controller
{

    /**
     * @var UserService
     */
    private UserService $userService;
    /**
     * @var AuthService
     */
    private AuthService $authService;

    public function __construct(
        $id,
        $module,
        UserService $userService,
        AuthService $authService,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->userService = $userService;
        $this->authService = $authService;
    }

    public function actions()
    {
        return [
            'options' => [
                'class' => OptionsAction::class
            ]
        ];
    }

    public function actionLogin()
    {
        $post = $this->request->post();
        $user = $this->userService->findByUsername($post['username']);
        if (!$user) {
            throw new UnauthorizedHttpException();
        }
        if (!$this->authService->validatePassword($user->hash, $post['password'])) {
            throw new UnauthorizedHttpException();
        }
        $token = $this->authService->generateAuthToken($user);
        $this->authService->generateRefreshToken(
            $user,
            $this->request->getRemoteIP(),
            $this->request->getUserAgent()
        );
        return [
            'token' => (string) $token
        ];
    }

    public function actionRefreshToken()
    {
        $refreshToken = $this->request->cookies->getValue('refresh-token', false);
        if (!$refreshToken) {
            throw new UnauthorizedHttpException('token not present');
        }
        $userRefreshToken = $this->authService->findRefreshToken($refreshToken);
        if (!$refreshToken)
            throw new UnauthorizedHttpException('not a valid refresh token');
        $user = $this->userService->findByUuid($userRefreshToken->user_id);
        if (!$user) {
            $userRefreshToken->delete();
            throw new UnauthorizedHttpException();
        }
        $token = $this->authService->generateAuthToken($user);
        return [
            'token' => $token
        ];
    }
}