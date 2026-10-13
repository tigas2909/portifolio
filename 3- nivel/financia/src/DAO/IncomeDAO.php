<?php

class IncomeDAO{
    private PDO $conn;

    public function __construct(PDO $conn){
        $this->conn = $conn;
    }

    //inserts
    public function create(array $data): array
    {
        $sql = "
            INSERT INTO income (
                user_id,
                description,
                amount,
                received_at
            )
            VALUES (
                :user_id,
                :description,
                :amount,
                :received_at
            )
        ";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':user_id' => $data['user_id'],
                ':description' => $data['description'],
                ':amount' => $data['amount'],
                ':received_at' => $data['received_at']
            ]);

            return [
                'success' => true,
                'id' => $this->conn->lastInsertId()
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'errors' => [$e->getMessage()]
            ];
        }
    }

    //selects
    public function selectAll(): array
    {
        $sql = "
            SELECT *
            FROM income
        ";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return [
                'success' => true,
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'errors' => [$e->getMessage()]
            ];
        }
    }

    public function selectById(int $id): array
    {
        $sql = "
            SELECT *
            FROM income
            WHERE id = :id
        ";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':id' => $id
            ]);

            return [
                'success' => true,
                'data' => $stmt->fetch(PDO::FETCH_ASSOC)
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'errors' => [$e->getMessage()]
            ];
        }
    }
}