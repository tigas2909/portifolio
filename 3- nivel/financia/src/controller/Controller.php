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

    // ganhos

    public function IncomeSelectALL(): ?array
    {
        return $this->incomeDao->SelectAll();
    }

    public function IncomeSelectById(int $id): ?array
    {
        return $this->incomeDao->SelectById($id);
    }

    public function IncomeCreate(array $dados = []): array
    {
        if (empty($dados)) {
            return [
                'success' => false,
                'errors' => ['Data cannot be empty.']
            ];
        }

        $data = CreateDTO::IncomeCreate($dados);

        if ($data['success'] === false) {
            return [
                'success' => false,
                'errors' => $data['result']
            ];
        }

        $result = $this->incomeDao->create($data['result']);

        if ($result['success'] === false) {
            return [
                'success' => false,
                'errors' => $result['errors']
            ];
        }

        return $result;
    }

    // transações

    public function TransactionSelectALL(): ?array
    {
        return $this->transactionDao->SelectAll();
    }

    public function TransactionSelectById(int $id): ?array
    {
        return $this->transactionDao->SelectById($id);
    }

    public function TransactionCreate(array $dados = []): array
    {
        if (empty($dados)) {
            return [
                'success' => false,
                'errors' => ['Data cannot be empty.']
            ];
        }

        
        $data = CreateDTO::TransactionCreate($dados);
        $result = $this->transactionDao->create($data['result']);

        return [
            'success' => false,
            'errors' => ['TransactionCreate not implemented yet.']
        ];
    }
}