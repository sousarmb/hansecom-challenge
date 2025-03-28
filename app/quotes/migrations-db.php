<?php

if (!isset($_ENV['MYSQL_QUOTES_DATABASE'])
    || !isset($_ENV['MYSQL_QUOTES_USER'])
    || !isset($_ENV['MYSQL_QUOTES_PASSWORD'])
) {
    echo 'Invalid database credentials' . PHP_EOL;
    exit(1);
}

return [
    'dbname' => $_ENV['MYSQL_QUOTES_DATABASE'],
    'user' => $_ENV['MYSQL_QUOTES_USER'],
    'password' => $_ENV['MYSQL_QUOTES_PASSWORD'],
    'host' => 'mysql',
    'driver' => 'pdo_mysql',
];
