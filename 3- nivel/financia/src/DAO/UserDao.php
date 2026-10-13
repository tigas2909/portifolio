<?php

class UserDao
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public static function countUser(PDO $conn): int
    {
        $sql = "SELECT COUNT(id) FROM users";

        $stmt = $conn->query($sql);

        return (int) $stmt->fetchColumn();
    }

    public function insert(
        string $name,
        string $email,
        string $password
    ): bool {
        $sql = "INSERT INTO users (
                    name,
                    email,
                    password,
                    password_type
                ) VALUES (
                    :name,
                    :email,
                    :password,
                    'TEMPORARY'
                )";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $password
        ]);
    }

    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT
                    id,
                    email,
                    password,
                    password_type,
                FROM users
                WHERE email = :email
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':email' => $email
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user !== false ? $user : null;
    }

    public function updatePassword(
        int $id,
        string $password
    ): bool {
        $sql = "UPDATE users
                SET password = :password,
                    password_type = 'PERMANENT'
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':password' => $password
        ]);
    }
}