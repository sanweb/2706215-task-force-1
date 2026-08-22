<?php

/*
// yii2-app-basic models User private static array $_users
$_users = [
    '100' => [
        'id' => '100',
        'username' => 'admin',
        // password: admin
        'passwordHash' => '$2y$13$gYAywKSkhfZDq9FLNdm7buKnvlRxDexf5xipSMAxQPDUxpaptmZJu',
        'authKey' => 'test100key',
        'accessToken' => '100-token',
    ],
    '101' => [
        'id' => '101',
        'username' => 'demo',
        // password: demo
        'passwordHash' => '$2y$13$alRLq1PGVMlGYwS/Y3iy3ewQns1Z8ol8Iq6Zb5k7ZwEhblA1aL29y',
        'authKey' => 'test101key',
        'accessToken' => '101-token',
    ],
];
*/
return [
    'admin' => [
        'id' => 100,
        'email' => 'admin@example.com',
        'name' => 'Admin',
        'password' => '$2y$12$Gaj5iA7mP.zwaEF9gPFRPuL1LbkhDK/cfUbhNh/Y2nxq4CCqz3pCS',
        'is_executor' => 0,
    ],
    'demo' => [
        'id' => '101',
        'email' => 'demo@example.com',
        'name' => 'Demo',
        'password' => '$2y$12$33iiq7uBDQkSnYdfVDxgfecXp6V4CYna.uDhOcB2lcT4bEBLXKqda',
        'is_executor' => 0,
    ],
];
