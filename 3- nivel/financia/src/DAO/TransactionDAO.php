<?php

class TransactionDAO{
    private PDO $conn;

    public function __construct(PDO $conn){
        $this->conn = $conn;
    }

    //selects
    public function selectAll(): array
    {
        $sql = "
            SELECT *
            FROM transactions
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
            FROM transactions
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

    //inserts
}