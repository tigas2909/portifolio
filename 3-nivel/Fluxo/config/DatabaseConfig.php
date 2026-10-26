<?php

require_once __DIR__ . '/config.php';

class DatabaseConfig
{
    public static function conectar(): PDO
    {
        $dsn = 'mysql:host=' . HOST
             . ';dbname=' . DATABASE
             . ';charset=utf8mb4';

        return new PDO($dsn, USERNAME, PASSWORD_DATABASE, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    }
}