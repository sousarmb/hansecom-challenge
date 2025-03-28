<?php

namespace App\Helpers;

use PDO\Mysql;

abstract class MySqlConnection
{
    private static ?Mysql $conn = null;

    public static function getConnection() {
        if (is_null(self::$conn)) {
            $dsn = "mysql:host=mysql;port=3306;dbname={$_ENV['MYSQL_QUOTES_DATABASE']}";
            static::$conn = new Mysql(
                $dsn,
                $_ENV['MYSQL_QUOTES_USER'],
                $_ENV['MYSQL_QUOTES_PASSWORD'],
                [
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                ]
            );
        }

        return static::$conn;
    }
}