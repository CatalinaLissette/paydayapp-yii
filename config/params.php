<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'jwt' => [
        'issuer' => 'https://api.example.com',  //name of your project (for information only)
        'audience' => 'https://frontend.example.com',  //description of the audience, eg. the website using the authentication (for info only)
        'id' => '',  //a unique identifier for the JWT, typically a random string
        'expire' => 86400,
    ],
    'getnet' => [
        'login' => '42706f40bac8b72331210da246fa71c9',
        'secretKey' => 'mssSX27S6aH8nqfm',
        'success' => env("GETNET_SUCCESS"),
        'fail' => env("GETNET_FAIL"),
        'returnUrl' => env("GETNET_RETURN_URL")
    ]
];
