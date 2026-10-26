<?php

require_once __DIR__ . '/MonthRange.php';

class IncomeDAO
{
    private const COLUMNS = 'id, description, amount, received_at, created_at';

    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    private function normalize(array $row): array
    {
        $row['id'] = (int) $row['id'];
        $row['amount'] = (float) $row['amount'];

        return $row;
    }

    private function fail(string $context, PDOException $e, string $message): array
    {
        error_log($context . ' ' . $e->getMessage());

        return [
            'success' => false,
            'errors' => [$message]
        ];
    }

    private function notFound(): array
    {
        return [
            'success' => false,
            'status' => 404,
            'errors' => ['Income not found.']
        ];
    }

    private function exists(int $id, int $userId): bool
    {
        $stmt = $this->conn->prepare(
            'SELECT 1 FROM income WHERE id = :id AND user_id = :user_id'
        );

        $stmt->execute([':id' => $id, ':user_id' => $userId]);

        return $stmt->fetchColumn() !== false;
    }

    // inserts
    public function create(array $data): array
    {
        $sql = "
            INSERT INTO income (user_id, description, amount, received_at)
            VALUES (:user_id, :description, :amount, :received_at)
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
                'id' => (int) $this->conn->lastInsertId()
            ];
        } catch (PDOException $e) {
            return $this->fail('IncomeDAO::create', $e, 'Failed to create income.');
        }
    }

    // selects
    public function selectAll(int $userId, ?string $month = null): array
    {
        $range = MonthRange::from($month);

        if ($range === null) {
            return [
                'success' => false,
                'status' => 400,
                'errors' => ['Invalid month. Use YYYY-MM.']
            ];
        }

        [$start, $end] = $range;

        $sql = "
            SELECT " . self::COLUMNS . "
            FROM income
            WHERE user_id = :user_id
              AND received_at >= :start
              AND received_at < :end
            ORDER BY received_at DESC, id DESC
        ";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':user_id' => $userId,
                ':start' => $start,
                ':end' => $end
            ]);

            return [
                'success' => true,
                'data' => array_map([$this, 'normalize'], $stmt->fetchAll(PDO::FETCH_ASSOC))
            ];
        } catch (PDOException $e) {
            return $this->fail('IncomeDAO::selectAll', $e, 'Failed to retrieve incomes.');
        }
    }

    public function selectById(int $id, int $userId): array
    {
        $sql = "
            SELECT " . self::COLUMNS . "
            FROM income
            WHERE id = :id AND user_id = :user_id
        ";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':id' => $id,
                ':user_id' => $userId
            ]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'success' => true,
                'data' => $row === false ? null : $this->normalize($row)
            ];
        } catch (PDOException $e) {
            return $this->fail('IncomeDAO::selectById', $e, 'Failed to retrieve income.');
        }
    }

    // updates
    public function update(int $id, int $userId, array $data): array
    {
        $sql = "
            UPDATE income
            SET description = :description,
                amount = :amount,
                received_at = :received_at
            WHERE id = :id AND user_id = :user_id
        ";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':description' => $data['description'],
                ':amount' => $data['amount'],
                ':received_at' => $data['received_at'],
                ':id' => $id,
                ':user_id' => $userId
            ]);

            // rowCount() também é 0 quando nada mudou, então confere se o registro existe.
            if ($stmt->rowCount() === 0 && !$this->exists($id, $userId)) {
                return $this->notFound();
            }

            return ['success' => true];
        } catch (PDOException $e) {
            return $this->fail('IncomeDAO::update', $e, 'Failed to update income.');
        }
    }

    // deletes
    public function delete(int $id, int $userId): array
    {
        try {
            $stmt = $this->conn->prepare(
                'DELETE FROM income WHERE id = :id AND user_id = :user_id'
            );

            $stmt->execute([':id' => $id, ':user_id' => $userId]);

            if ($stmt->rowCount() === 0) {
                return $this->notFound();
            }

            return ['success' => true];
        } catch (PDOException $e) {
            return $this->fail('IncomeDAO::delete', $e, 'Failed to delete income.');
        }
    }
}