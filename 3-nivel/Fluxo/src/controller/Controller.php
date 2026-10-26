<?php

require_once __DIR__ . '/../jwt/ConfigJWT.php';
require_once __DIR__ . '/../DAO/IncomeDAO.php';
require_once __DIR__ . '/../DAO/TransactionDAO.php';
require_once __DIR__ . '/../DTOs/CreateDTO.php';

class Controller
{
    private const AUTH_ERROR = 'Authentication is required or the token is invalid.';

    private IncomeDAO $incomeDao;
    private TransactionDAO $transactionDao;

    public function __construct(PDO $conn)
    {
        $this->incomeDao = new IncomeDAO($conn);
        $this->transactionDao = new TransactionDAO($conn);
    }

    // =========================
    // AUXILIARES
    // =========================

    private function respond(int $status, array $body): array
    {
        http_response_code($status);

        return $body;
    }

    private function error(int $status, array|string $errors): array
    {
        return $this->respond($status, [
            'success' => false,
            'errors' => (array) $errors
        ]);
    }

    // O id do usuário sempre vem do token (cookie), nunca do corpo da requisição.
    private function authenticatedUserId(): int|false
    {
        $token = $_COOKIE['token'] ?? null;

        if (!is_string($token) || $token === '') {
            return false;
        }

        return ConfigJWT::getUserId($token);
    }

    private function isPositiveId(mixed $value): bool
    {
        return (
            is_int($value)
            || (is_string($value) && ctype_digit($value))
        ) && (int) $value > 0;
    }

    private function isValidDate(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        return $date !== false && $date->format('Y-m-d') === $value;
    }

    // "data": {"month": "2026-10"}. Retorna null (mês atual) se não vier,
    // ou false se o valor não for texto.
    private function readMonth(array $datas): string|null|false
    {
        $month = $datas['month'] ?? null;

        if ($month !== null && !is_string($month)) {
            return false;
        }

        return $month;
    }

    // Executa a operação e transforma qualquer exceção em erro 500 genérico.
    private function guard(string $label, string $message, callable $operation): array
    {
        try {
            return $operation();
        } catch (Throwable $e) {
            error_log($label . ' error: ' . $e->getMessage());

            return $this->error(500, $message);
        }
    }

    // Resposta de leitura: a lista (ou o item) vai em "result".
    private function fromRead(array $result, string $fallbackError): array
    {
        if (($result['success'] ?? false) !== true) {
            return $this->error(
                $result['status'] ?? 500,
                $result['errors'] ?? [$fallbackError]
            );
        }

        return $this->respond(200, [
            'success' => true,
            'result' => $result['data'] ?? []
        ]);
    }

    // Resposta de escrita (create, update, delete).
    private function fromWrite(array $result, string $fallbackError, int $okStatus = 200): array
    {
        if (($result['success'] ?? false) !== true) {
            return $this->error(
                $result['status'] ?? 500,
                $result['errors'] ?? [$fallbackError]
            );
        }

        return $this->respond($okStatus, $result);
    }

    // Erro de validação do DTO: 401 se for falha de autenticação, senão 400.
    private function dtoError(array $dto, string $fallbackError): array
    {
        $errors = $dto['result'] ?? [$fallbackError];
        $status = in_array(self::AUTH_ERROR, $errors, true) ? 401 : 400;

        return $this->error($status, $errors);
    }

    // =========================
    // GANHOS
    // =========================

    public function IncomeSelectAll(array $datas = []): array
    {
        $userId = $this->authenticatedUserId();

        if ($userId === false) {
            return $this->error(401, self::AUTH_ERROR);
        }

        $month = $this->readMonth($datas);

        if ($month === false) {
            return $this->error(400, 'Invalid month.');
        }

        return $this->guard(
            'IncomeSelectAll',
            'Failed to retrieve incomes.',
            fn () => $this->fromRead(
                $this->incomeDao->selectAll($userId, $month),
                'Failed to retrieve incomes.'
            )
        );
    }

    public function IncomeSelectById(int $id): array
    {
        if ($id <= 0) {
            return $this->error(400, 'Invalid income ID.');
        }

        $userId = $this->authenticatedUserId();

        if ($userId === false) {
            return $this->error(401, self::AUTH_ERROR);
        }

        return $this->guard(
            'IncomeSelectById',
            'Failed to retrieve income.',
            function () use ($id, $userId) {
                $result = $this->incomeDao->selectById($id, $userId);

                if (($result['success'] ?? false) === true && empty($result['data'])) {
                    return $this->error(404, 'Income not found.');
                }

                return $this->fromRead($result, 'Failed to retrieve income.');
            }
        );
    }

    public function IncomeCreate(array $dados = []): array
    {
        if (empty($dados)) {
            return $this->error(400, 'Data cannot be empty.');
        }

        return $this->guard(
            'IncomeCreate',
            'Internal server error while creating income.',
            function () use ($dados) {
                $dto = CreateDTO::IncomeCreate($dados);

                if (($dto['success'] ?? false) !== true) {
                    return $this->dtoError($dto, 'Invalid income data.');
                }

                return $this->fromWrite(
                    $this->incomeDao->create($dto['result']),
                    'Failed to create income.',
                    201
                );
            }
        );
    }

    // Recebe o id do ganho e os mesmos campos do create.
    public function IncomeUpdate(array $dados = []): array
    {
        if (empty($dados)) {
            return $this->error(400, 'Data cannot be empty.');
        }

        $id = $dados['id'] ?? null;

        if (!$this->isPositiveId($id)) {
            return $this->error(400, 'Invalid income ID.');
        }

        return $this->guard(
            'IncomeUpdate',
            'Internal server error while updating income.',
            function () use ($dados, $id) {
                $dto = CreateDTO::IncomeCreate($dados);

                if (($dto['success'] ?? false) !== true) {
                    return $this->dtoError($dto, 'Invalid income data.');
                }

                return $this->fromWrite(
                    $this->incomeDao->update((int) $id, (int) $dto['result']['user_id'], $dto['result']),
                    'Failed to update income.'
                );
            }
        );
    }

    public function IncomeDelete(array $dados = []): array
    {
        $id = $dados['id'] ?? null;

        if (!$this->isPositiveId($id)) {
            return $this->error(400, 'Invalid income ID.');
        }

        $userId = $this->authenticatedUserId();

        if ($userId === false) {
            return $this->error(401, self::AUTH_ERROR);
        }

        return $this->guard(
            'IncomeDelete',
            'Internal server error while deleting income.',
            fn () => $this->fromWrite(
                $this->incomeDao->delete((int) $id, $userId),
                'Failed to delete income.'
            )
        );
    }

    // =========================
    // TRANSAÇÕES
    // =========================

    public function TransactionSelectAll(array $datas = []): array
    {
        $userId = $this->authenticatedUserId();

        if ($userId === false) {
            return $this->error(401, self::AUTH_ERROR);
        }

        $month = $this->readMonth($datas);

        if ($month === false) {
            return $this->error(400, 'Invalid month.');
        }

        return $this->guard(
            'TransactionSelectAll',
            'Failed to retrieve transactions.',
            fn () => $this->fromRead(
                $this->transactionDao->selectAll($userId, $month),
                'Failed to retrieve transactions.'
            )
        );
    }

    public function TransactionSelectById(int $id): array
    {
        if ($id <= 0) {
            return $this->error(400, 'Invalid transaction ID.');
        }

        $userId = $this->authenticatedUserId();

        if ($userId === false) {
            return $this->error(401, self::AUTH_ERROR);
        }

        return $this->guard(
            'TransactionSelectById',
            'Failed to retrieve transaction.',
            function () use ($id, $userId) {
                $result = $this->transactionDao->selectById($id, $userId);

                if (($result['success'] ?? false) === true && empty($result['data'])) {
                    return $this->error(404, 'Transaction not found.');
                }

                return $this->fromRead($result, 'Failed to retrieve transaction.');
            }
        );
    }

    // Lista as parcelas de uma transação.
    public function TransactionInstallments(array $datas = []): array
    {
        $id = $datas['id'] ?? null;

        if (!$this->isPositiveId($id)) {
            return $this->error(400, 'Invalid transaction ID.');
        }

        $userId = $this->authenticatedUserId();

        if ($userId === false) {
            return $this->error(401, self::AUTH_ERROR);
        }

        return $this->guard(
            'TransactionInstallments',
            'Failed to retrieve installments.',
            fn () => $this->fromRead(
                $this->transactionDao->selectInstallments((int) $id, $userId),
                'Failed to retrieve installments.'
            )
        );
    }

    // Cria pela procedure INSERT_TRANSACTION (gera as parcelas).
    // Em INSTALLMENT, "amount" é o valor total e "total_installments" deve ser 2 ou mais.
    public function TransactionCreate(array $dados = []): array
    {
        if (empty($dados)) {
            return $this->error(400, 'Data cannot be empty.');
        }

        return $this->guard(
            'TransactionCreate',
            'Internal server error while creating transaction.',
            function () use ($dados) {
                $dto = CreateDTO::TransactionCreate($dados);

                if (($dto['success'] ?? false) !== true) {
                    return $this->dtoError($dto, 'Invalid transaction data.');
                }

                return $this->fromWrite(
                    $this->transactionDao->create($dto['result']),
                    'Failed to create transaction.',
                    201
                );
            }
        );
    }

    // Só a descrição pode ser alterada: valor, tipo, vencimento e parcelas
    // ficam presos às parcelas geradas pela procedure.
    public function TransactionUpdate(array $dados = []): array
    {
        $id = $dados['id'] ?? null;

        if (!$this->isPositiveId($id)) {
            return $this->error(400, 'Invalid transaction ID.');
        }

        $description = $dados['description'] ?? null;

        if (
            !is_string($description)
            || trim($description) === ''
            || mb_strlen(trim($description)) > 255
        ) {
            return $this->error(400, 'Description is required and must have up to 255 characters.');
        }

        $userId = $this->authenticatedUserId();

        if ($userId === false) {
            return $this->error(401, self::AUTH_ERROR);
        }

        return $this->guard(
            'TransactionUpdate',
            'Internal server error while updating transaction.',
            fn () => $this->fromWrite(
                $this->transactionDao->update((int) $id, $userId, trim($description)),
                'Failed to update transaction.'
            )
        );
    }

    // Paga ONE_TIME, RECURRING ou a parcela atual (procedure UPDATE_PAYMENT).
    public function TransactionPay(array $dados = []): array
    {
        $id = $dados['id'] ?? null;

        if (!$this->isPositiveId($id)) {
            return $this->error(400, 'Invalid transaction ID.');
        }

        $userId = $this->authenticatedUserId();

        if ($userId === false) {
            return $this->error(401, self::AUTH_ERROR);
        }

        return $this->guard(
            'TransactionPay',
            'Internal server error while registering payment.',
            fn () => $this->fromRead(
                $this->transactionDao->pay((int) $id, $userId),
                'Failed to register payment.'
            )
        );
    }

    // Paga ou antecipa uma parcela pelo vencimento (procedure PAY_INSTALLMENT).
    public function TransactionPayInstallment(array $dados = []): array
    {
        $id = $dados['id'] ?? null;

        if (!$this->isPositiveId($id)) {
            return $this->error(400, 'Invalid transaction ID.');
        }

        $dueDate = $dados['due_date'] ?? null;

        if (!$this->isValidDate($dueDate)) {
            return $this->error(400, 'Due date must be a valid date in YYYY-MM-DD format.');
        }

        $userId = $this->authenticatedUserId();

        if ($userId === false) {
            return $this->error(401, self::AUTH_ERROR);
        }

        return $this->guard(
            'TransactionPayInstallment',
            'Internal server error while registering installment payment.',
            fn () => $this->fromRead(
                $this->transactionDao->payInstallment((int) $id, $userId, $dueDate),
                'Failed to register installment payment.'
            )
        );
    }

    public function TransactionDelete(array $dados = []): array
    {
        $id = $dados['id'] ?? null;

        if (!$this->isPositiveId($id)) {
            return $this->error(400, 'Invalid transaction ID.');
        }

        $userId = $this->authenticatedUserId();

        if ($userId === false) {
            return $this->error(401, self::AUTH_ERROR);
        }

        return $this->guard(
            'TransactionDelete',
            'Internal server error while deleting transaction.',
            fn () => $this->fromWrite(
                $this->transactionDao->delete((int) $id, $userId),
                'Failed to delete transaction.'
            )
        );
    }
}