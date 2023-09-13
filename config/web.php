<?php

use sizeg\jwt\Jwt;
use sizeg\jwt\JwtValidationData;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'es',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules' => [
        'v1' => [
            'class' => 'app\api\v1\Module',
        ],
    ],
    'components' => [

        'jwt' => [
            'class' => Jwt::class,
            'key' => 'lqNCkvEXt__5jLmIkUk6AUnRLj4K_qk8',
            'jwtValidationData' => JwtValidationData::class
        ],
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser'
            ],
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'lqNCkvEXt__5jLmIkUk6AUnRLj4K_qk8',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com', // Configura el host del servidor de correo saliente
                'username' => 'payggodev03@gmail.com',
                'password' => 'Admin01.',
                'port' => '587',
                'encryption' => 'tls',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/user', 'v1/auth', 'v1/region', 'v1/provider'
                    ],
                ],
                'POST v1/regions/<region_id:\d+>/commune' => 'v1/commune/create',
                'v1/regions/<region_id:\d+>/commune' => 'v1/commune/search-by-region-id',
                'POST v1/sales' => 'v1/order/create',
                'v1/sales' => 'v1/order',
                'v1/sales/<order_id:\d+>' => 'v1/order/view',
                'v1/sales/commerce/<commerce_id:>' => 'v1/order/by-commerce',
                'v1/sales/provider/<provider_id:>' => 'v1/order/by-provider',
                'GET v1/sales/payment/status/<payment_id:>' => 'v1/order/status-quotes-payment',
                'v1/quotes/khipu/create-payment' => 'v1/quote/create-payment',
                'POST v1/quotes/khipu/notification/<reference_id:>' => 'v1/quote/notification',
                'v1/khipu-account' => 'v1/khipu-account',
                'v1/khipu-account/<provider_id:\d+>' => 'v1/khipu-account/search-by-provider-id',
                'POST v1/khipu-account' => 'v1/khipu-account/create',
                //'POST v1/region' => 'v1/region/create',
                'POST v1/users' => 'v1/user/create',
                'POST v1/offer' => 'v1/offer/create',
                'PUT v1/offer/<id:\d+>' => 'v1/offer/update',
                'v1/offer/provider/<provider_id:\d+>' => 'v1/offer/by-provider',
                'GET v1/commerce/<user_id:[\w-]{36}>/providers' => 'v1/commerce/providers',
                'POST v1/commerce/enroll' => 'v1/commerce/enroll',
                'PUT v1/commerce/enroll' => 'v1/commerce/update-enrollment'
            ],
        ],

    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '*'],
    ];
}

return $config;
