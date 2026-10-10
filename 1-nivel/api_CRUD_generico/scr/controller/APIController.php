<?php
require_once __DIR__ . '/../repository/APIRepository.php';

class APIController{

    private APIRepository $APIRepository;
    public function __construct(PDO $conn){
        $this->APIRepository = new APIRepository($conn);

    }

    //insert method=POST
    public function insert(String $table, array $dados): bool  {
        return $this->APIRepository->create($table, $dados);
    }
    
    //selects method=GET
    public function selectAll(string $table): ?array{
        return $this->APIRepository->find($table);
    }

    public function selectById(string $table, int $id): ?array{
        return $this->APIRepository->findById($table, $id);
    }

    public function selectByActive(string $table): ?array{
        return $this->APIRepository->findByActive($table);
    }

    public function selectByDeactive(string $table): ?array{
        return $this->APIRepository->findByDeactive($table);
    }

    //update  method=PUT
    public function update(int $id, string $table, array $dados): bool{
        return $this->APIRepository->saveChanges($table, $id, $dados);
    }

    public function delete(string $table, int $id): bool{
        return $this->APIRepository->deactivate($table, $id);
    }
}