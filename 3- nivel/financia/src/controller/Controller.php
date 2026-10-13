<?php

require_once __DIR__ . '/../DAO/IncomeDAO.php';
require_once __DIR__ . '/../DAO/TransactionDAO.php';
require_once __DIR__ . '/../DTOs/CreateDTO.php';

class Controller
{
    private IncomeDAO $incomeDao;
    private TransactionDAO $transactionDao;

    public function __construct(PDO $conn)
    {
        $this->incomeDao = new IncomeDAO($conn);
        $this->transactionDao = new TransactionDAO($conn);
    }

    // =========================
    // GANHOS
    // =========================

    public function IncomeSelectALL(): array
    {
        try {
            $result = $this->incomeDao->SelectAll();

            http_response_code(200);

            return [
                'success' => true,
                'result' => $result ?? []
            ];
        } catch (Throwable $e) {
            error_log('IncomeSelectALL error: ' . $e->getMessage());

            http_response_code(500);

            return [
                'success' => false,
                'errors' => ['Failed to retrieve incomes.']
            ];
        }
    }

    public function IncomeSelectById(int $id): array
    {
        if ($id <= 0) {
            http_response_code(400);

            return [
                'success' => false,
                'errors' => ['Invalid income ID.']
            ];
        }

        try {
            $result = $this->incomeDao->SelectById($id);

            if ($result === null || $result === false) {
                http_response_code(404);

                return [
                    'success' => false,
                    'errors' => ['Income not found.']
                ];
            }

            http_response_code(200);

            return [
                'success' => true,
                'result' => $result
            ];
        } catch (Throwable $e) {
            error_log('IncomeSelectById error: ' . $e->getMessage());

            http_response_code(500);

            return [
                'success' => false,
                'errors' => ['Failed to retrieve income.']
            ];
        }
    }

    public function IncomeCreate(array $dados = []): array
    {
        if (empty($dados)) {
            http_response_code(400);

            return [
                'success' => false,
                'errors' => ['Data cannot be empty.']
            ];
        }

        try {
            $data = CreateDTO::IncomeCreate($dados);

            if (($data['success'] ?? false) !== true) {
                $errors = $data['result'] ?? ['Invalid income data.'];

                $status = in_array(
                    'Authentication is required or the token is invalid.',
                    $errors,
                    true
                ) ? 401 : 400;

                http_response_code($status);

                return [
                    'success' => false,
                    'errors' => $errors
                ];
            }

            $result = $this->incomeDao->create($data['result']);

            if (
                !is_array($result) ||
                ($result['success'] ?? false) !== true
            ) {
                http_response_code(500);

                return [
                    'success' => false,
                    'errors' => $result['errors'] ?? ['Failed to create income.']
                ];
            }

            http_response_code(201);

            return $result;
        } catch (PDOException $e) {
            error_log('IncomeCreate database error: ' . $e->getMessage());

            http_response_code(500);

            return [
                'success' => false,
                'errors' => ['Database error while creating income.']
            ];
        } catch (Throwable $e) {
            error_log('IncomeCreate error: ' . $e->getMessage());

            http_response_code(500);

            return [
                'success' => false,
                'errors' => ['Internal server error while creating income.']
            ];
        }
    }

    // =========================
    // TRANSAÇÕES
    // =========================

    public function TransactionSelectALL(): array
    {
        try {
            $result = $this->transactionDao->SelectAll();

            http_response_code(200);

            return [
                'success' => true,
                'result' => $result ?? []
            ];
        } catch (Throwable $e) {
            error_log('TransactionSelectALL error: ' . $e->getMessage());

            http_response_code(500);

            return [
                'success' => false,
                'errors' => ['Failed to retrieve transactions.']
            ];
        }
    }

    public function TransactionSelectById(int $id): array
    {
        if ($id <= 0) {
            http_response_code(400);

            return [
                'success' => false,
                'errors' => ['Invalid transaction ID.']
            ];
        }

        try {
            $result = $this->transactionDao->SelectById($id);

            if ($result === null || $result === false) {
                http_response_code(404);

                return [
                    'success' => false,
                    'errors' => ['Transaction not found.']
                ];
            }

            http_response_code(200);

            return [
                'success' => true,
                'result' => $result
            ];
        } catch (Throwable $e) {
            error_log('TransactionSelectById error: ' . $e->getMessage());

            http_response_code(500);

            return [
                'success' => false,
                'errors' => ['Failed to retrieve transaction.']
            ];
        }
    }

    public function TransactionCreate(array $dados = []): array
    {
        if (empty($dados)) {
            http_response_code(400);

            return [
                'success' => false,
                'errors' => ['Data cannot be empty.']
            ];
        }

        try {
            $data = CreateDTO::TransactionCreate($dados);

            if (($data['success'] ?? false) !== true) {
                $errors = $data['result'] ?? ['Invalid transaction data.'];

                $status = in_array(
                    'Authentication is required or the token is invalid.',
                    $errors,
                    true
                ) ? 401 : 400;

                http_response_code($status);

                return [
                    'success' => false,
                    'errors' => $errors
                ];
            }

            $result = $this->transactionDao->create($data['result']);

            if (
                !is_array($result) ||
                ($result['success'] ?? false) !== true
            ) {
                http_response_code(500);

                return [
                    'success' => false,
                    'errors' => $result['errors'] ?? ['Failed to create transaction.']
                ];
            }

            http_response_code(201);

            return $result;
        } catch (PDOException $e) {
            error_log('TransactionCreate database error: ' . $e->getMessage());

            http_response_code(500);

            return [
                'success' => false,
                'errors' => ['Database error while creating transaction.']
            ];
        } catch (Throwable $e) {
            error_log('TransactionCreate error: ' . $e->getMessage());

            http_response_code(500);

            return [
                'success' => false,
                'errors' => ['Internal server error while creating transaction.']
            ];
        }
    }
}