<?php

$db = require __DIR__ . '/db.php';
// test database! Important not to run tests on production or development databases
$db['dsn'] = sprintf(
    'mysql:host=%s;port=%s;dbname=%s',
    $_ENV['DB_HOST'],
    $_ENV['DB_PORT'],
    'taskforce_yii2_test',
);

return $db;
