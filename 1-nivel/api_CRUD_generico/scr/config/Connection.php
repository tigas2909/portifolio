<?php

class Connection{
    public static function create(array $db): PDO{
        return new PDO(
            $db['dsn'],
            $db['username'], 
            $db['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }
}