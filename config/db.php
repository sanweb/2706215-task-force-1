<?php

return [
    'class' => \yii\db\Connection::class,
    'dsn' => sprintf(
        'mysql:host=%s;port=%s;dbname=%s',
        $_ENV['DB_HOST'],
        $_ENV['DB_PORT'],
        $_ENV['DB_NAME'],
    ),
    'username' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASSWORD'],
    'charset' => 'utf8mb4',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
