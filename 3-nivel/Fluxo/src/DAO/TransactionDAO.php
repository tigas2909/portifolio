<?php

require_once __DIR__ . '/MonthRange.php';

class TransactionDAO
{
    private const COLUMNS = 'id, description, amount, type, status, due_date, paid_at, total_installments, paid_installments, created_at';

    // Mensagens lançadas pelas procedures (SIGNAL) e o status HTTP de cada uma.
    private const BUSINESS_STATUS = [
        'Transação não encontrada' => 404,
        'Parcela não encontrada ou já paga' => 404,
        'Esta transação já foi concluída' => 409,
        'A transação já foi concluída' => 409,
    ];

    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    // =========================
    // AUXILIARES
    // =========================

    private function normalize(array $row): array
    {
        $row['id'] = (int) $row['id'];
        $row['amount'] = (float) $row['amount'];

        foreach (['installment_id', 'number', 'total_installments', 'paid_installments'] as $field) {
            if (array_key_exists($field, $row) && $row[$field] !== null) {
                $row[$field] = (int) $row[$field];
            }
        }

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

    // Erros lançados pelas procedures (SQLSTATE 45000) viram mensagem para o usuário.
    private function procedureFail(string $context, PDOException $e, string $fallback): array
    {
        if (($e->errorInfo[0] ?? '') === '45000') {
            $message = (string) ($e->errorInfo[2] ?? $fallback);

            return [
                'success' => false,
                'status' => self::BUSINESS_STATUS[$message] ?? 400,
                'errors' => [$message]
            ];
        }

        return $this->fail($context, $e, $fallback);
    }

    private function notFound(): array
    {
        return [
            'success' => false,
            'status' => 404,
            'errors' => ['Transaction not found.']
        ];
    }

    private function exists(int $id, int $userId): bool
    {
        $stmt = $this->conn->prepare(
            'SELECT 1 FROM transactions WHERE id = :id AND user_id = :user_id'
        );

        $stmt->execute([':id' => $id, ':user_id' => $userId]);

        return $stmt->fetchColumn() !== false;
    }

    // Executa uma procedure e devolve a primeira linha do resultado.
    private function call(string $sql, array $params): array
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        // Libera os resultados extras que a procedure deixa abertos.
        $stmt->closeCursor();

        return $row;
    }

    // =========================
    // INSERTS (procedure INSERT_TRANSACTION: cria a transação e as parcelas)
    // =========================

    public function create(array $data): array
    {
        try {
            $row = $this->call(
                'CALL INSERT_TRANSACTION(:user_id, :description, :amount, :type, :due_date, :total_installments)',
                [
                    ':user_id' => $data['user_id'],
                    ':description' => $data['description'],
                    ':amount' => $data['amount'],
                    ':type' => $data['type'],
                    ':due_date' => $data['due_date'],
                    ':total_installments' => (int) ($data['total_installments'] ?? 1)
                ]
            );

            return [
                'success' => true,
                'id' => (int) ($row['transaction_id'] ?? 0)
            ];
        } catch (PDOException $e) {
            return $this->procedureFail('TransactionDAO::create', $e, 'Failed to create transaction.');
        }
    }

    // =========================
    // SELECTS
    // =========================

    // O mês lista os ONE_TIME (por due_date) e as parcelas que vencem no mês.
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
            SELECT
                t.id AS id,
                NULL AS installment_id,
                t.description AS description,
                t.amount AS amount,
                t.type AS type,
                t.status AS status,
                t.due_date AS due_date,
                t.paid_at AS paid_at,
                1 AS number,
                t.total_installments AS total_installments,
                t.paid_installments AS paid_installments
            FROM transactions t
            WHERE t.user_id = :user_one
              AND t.type = 'ONE_TIME'
              AND t.due_date >= :start_one
              AND t.due_date < :end_one

            UNION ALL

            SELECT
                t.id,
                i.id,
                t.description,
                i.amount,
                t.type,
                i.status,
                i.due_date,
                i.paid_at,
                (
                    SELECT COUNT(*)
                    FROM installments x
                    WHERE x.transaction_id = i.transaction_id
                      AND (
                          x.due_date < i.due_date
                          OR (x.due_date = i.due_date AND x.id <= i.id)
                      )
                ),
                t.total_installments,
                t.paid_installments
            FROM installments i
            INNER JOIN transactions t ON t.id = i.transaction_id
            WHERE t.user_id = :user_two
              AND i.due_date >= :start_two
              AND i.due_date < :end_two

            ORDER BY due_date DESC, id DESC
        ";

        try {
            $stmt = $this->conn->prepare($sql);

            // Com prepares nativos, um mesmo nome não pode se repetir na consulta.
            $stmt->execute([
                ':user_one' => $userId,
                ':start_one' => $start,
                ':end_one' => $end,
                ':user_two' => $userId,
                ':start_two' => $start,
                ':end_two' => $end
            ]);

            return [
                'success' => true,
                'data' => array_map([$this, 'normalize'], $stmt->fetchAll(PDO::FETCH_ASSOC))
            ];
        } catch (PDOException $e) {
            return $this->fail('TransactionDAO::selectAll', $e, 'Failed to retrieve transactions.');
        }
    }

    public function selectById(int $id, int $userId): array
    {
        $sql = "
            SELECT " . self::COLUMNS . "
            FROM transactions
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
            return $this->fail('TransactionDAO::selectById', $e, 'Failed to retrieve transaction.');
        }
    }

    // Parcelas de uma transação (para escolher qual antecipar).
    public function selectInstallments(int $id, int $userId): array
    {
        try {
            if (!$this->exists($id, $userId)) {
                return $this->notFound();
            }

            $stmt = $this->conn->prepare("
                SELECT id, amount, due_date, status, paid_at
                FROM installments
                WHERE transaction_id = :transaction_id
                ORDER BY due_date, id
            ");

            $stmt->execute([':transaction_id' => $id]);

            return [
                'success' => true,
                'data' => array_map([$this, 'normalize'], $stmt->fetchAll(PDO::FETCH_ASSOC))
            ];
        } catch (PDOException $e) {
            return $this->fail('TransactionDAO::selectInstallments', $e, 'Failed to retrieve installments.');
        }
    }

    // =========================
    // PAGAMENTOS
    // =========================

    // Procedure UPDATE_PAYMENT: ONE_TIME, RECURRING e parcela atual.
    public function pay(int $id, int $userId): array
    {
        try {
            return [
                'success' => true,
                'data' => $this->call(
                    'CALL UPDATE_PAYMENT(:user_id, :transaction_id)',
                    [':user_id' => $userId, ':transaction_id' => $id]
                )
            ];
        } catch (PDOException $e) {
            return $this->procedureFail('TransactionDAO::pay', $e, 'Failed to register payment.');
        }
    }

    // Procedure PAY_INSTALLMENT: paga ou antecipa a parcela com este vencimento.
    public function payInstallment(int $id, int $userId, string $dueDate): array
    {
        try {
            return [
                'success' => true,
                'data' => $this->call(
                    'CALL PAY_INSTALLMENT(:user_id, :transaction_id, :due_date)',
                    [':user_id' => $userId, ':transaction_id' => $id, ':due_date' => $dueDate]
                )
            ];
        } catch (PDOException $e) {
            return $this->procedureFail('TransactionDAO::payInstallment', $e, 'Failed to register installment payment.');
        }
    }

    // =========================
    // UPDATES (só a descrição; o resto fica preso às parcelas geradas)
    // =========================

    public function update(int $id, int $userId, string $description): array
    {
        try {
            $stmt = $this->conn->prepare("
                UPDATE transactions
                SET description = :description
                WHERE id = :id AND user_id = :user_id
            ");

            $stmt->execute([
                ':description' => $description,
                ':id' => $id,
                ':user_id' => $userId
            ]);

            // rowCount() também é 0 quando nada mudou, então confere se o registro existe.
            if ($stmt->rowCount() === 0 && !$this->exists($id, $userId)) {
                return $this->notFound();
            }

            return ['success' => true];
        } catch (PDOException $e) {
            return $this->fail('TransactionDAO::update', $e, 'Failed to update transaction.');
        }
    }

    // =========================
    // DELETES (as parcelas somem junto, por causa do ON DELETE CASCADE)
    // =========================

    public function delete(int $id, int $userId): array
    {
        try {
            $stmt = $this->conn->prepare(
                'DELETE FROM transactions WHERE id = :id AND user_id = :user_id'
            );

            $stmt->execute([':id' => $id, ':user_id' => $userId]);

            if ($stmt->rowCount() === 0) {
                return $this->notFound();
            }

            return ['success' => true];
        } catch (PDOException $e) {
            return $this->fail('TransactionDAO::delete', $e, 'Failed to delete transaction.');
        }
    }
}