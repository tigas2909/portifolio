<?php

class UserDao{
    private PDO $conn;

    public function __construct(PDO $conn){
        $this->conn = $conn;
    }

    public static function countUser(PDO $conn): int {
        $sql = "SELECT COUNT(id) FROM users";

        $stmt = $conn->query($sql);

        return (int) $stmt->fetchColumn();
    }
}