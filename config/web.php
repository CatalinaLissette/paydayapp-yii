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
        '@npm' => '@vendor/npm-asset',
    ],
    'modules' => [
        'v1' => [
            'class' => 'app\api\v1\Module',
        ],
    ],
    'components' => [
        'response' => [
            'class' => \yii\web\Response::class,
            'on beforeSend' => function ($event) {
                header("Access-Control-Allow-Credentials: true");
                header("Access-Control-Allow-Headers: X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version, Authorization");
                header("Access-Control-Allow-Methods: PUT, GET, POST, OPTION, DELETE");
                header("Access-Control-Allow-Origin: *");
            }
        ],
        'jwt' => [
            'class' => Jwt::class,
            'key' => 'lqNCkvEXt__5jLmIkUk6AUnRLj4K_qk8',
            'jwtValidationData' => \app\components\jwt\JwtValidationData::class,
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
        'httpclient' => [
            'class' => 'yii\httpclient\Client',
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/user', 'v1/auth', 'v1/region', 'v1/provider', 'v1/commerce'
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
                'POST v1/quotes/khipu/create-payment' => 'v1/quote/create-payment',
                'POST v1/quotes/khipu/delete-payment' => 'v1/quote/delete-payment',
                'POST v1/quotes/khipu/notification/<reference_id:>' => 'v1/notification-khipu/notification',
                'POST,GET v1/quotes/khipu/notification/cancel-payment/<quote_id:>' => 'v1/notification-khipu/cancel-payment',
                'v1/khipu-account/<provider_id:\d+>' => 'v1/khipu-account/search-by-provider-id',
                'POST v1/khipu-account' => 'v1/khipu-account/create',
                'GET v1/khipu-account' => 'v1/khipu-account/search-all',
                'POST v1/users' => 'v1/user/create',
                'POST v1/offer' => 'v1/offer/create',
                'PUT v1/offer/<id:\d+>' => 'v1/offer/update',
                'v1/offer/provider/<provider_id:\d+>' => 'v1/offer/by-provider',
                'GET,OPTIONS v1/commerce/<user_id:[\w-]{36}>/providers' => 'v1/commerce/providers',
                'GET,OPTIONS v1/provider/<user_id:[\w-]{36}>/commerces' => 'v1/provider/commerces',
                'v1/commune/region/<regionId:\d+>' => 'v1/commune/region',
                'GET v1/provider/<uuid:[\w-]{36}>' => 'v1/provider/view',
                'PUT v1/provider/<uuid:[\w-]{36}' => 'v1/provider/update',
                'POST v1/commerce/enroll' => 'v1/commerce/enroll',
                'PUT v1/commerce/enroll' => 'v1/commerce/update-enrollment',
                'PUT v1/commerce/credit' => 'v1/commerce/update-credit',
                'GET v1/commerce/<user_id:[\w-]{36}>/provider/<provider_id:\d+>' => 'v1/commerce/provider-commerce',
                'GET v1/commerce/<commerce_id:\d+>/providers' => 'v1/commerce/enrollment-by-commerce',
                'POST v1/getnet/subscription' => 'v1/get-net-click/create-subscription',
                'POST v1/getnet/subscription/<request_id:>' => 'v1/get-net-click/get-request-information',
                'POST v1/getnet/collect' => 'v1/get-net-click/collect',
                'POST v1/getnet/invalidate' => 'v1/get-net-click/invalidate',
                'GET v1/surcharge/search' => 'v1/surcharge/view',
                'PUT v1/surcharge/update/<id:\d+>' => 'v1/surcharge/update',
                'POST v1/user/change-password' => 'v1/user/change-password',
                'PUT v1/commerce/<commerce_id:\d+>/disable' => 'v1/commerce/disable',

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
