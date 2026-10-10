<?php

class APIRepository
{
    private PDO $connection;

    private const PRE_TABLE = 'tb_';

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    //insert
    public function create(string $table, array $data): bool
    {
        $table = self::PRE_TABLE . $table;

        $columns = array_keys($data);

        $fields = implode(', ', $columns);

        $placeholders = ':' . implode(', :', $columns);

        $sql = "INSERT INTO {$table} ({$fields})
                VALUES ({$placeholders})";

        $statement = $this->connection->prepare($sql);

        return $statement->execute($data);
    }

    //selects
    public function find(string $table): ?array
    {
        $table = self::PRE_TABLE . $table;

        $sql = "SELECT * FROM {$table}";

        $statement = $this->connection->query($sql);

        return $statement->fetchAll();
    }

    public function findById(string $table, int $id): ?array
    {
        $primaryKey = 'id_' . $table;
        $table = self::PRE_TABLE . $table;

        $sql = "SELECT * FROM {$table} WHERE {$primaryKey} = :id";

        $statement = $this->connection->prepare($sql);

        $statement->execute([
            'id' => $id
        ]);

        $result = $statement->fetch();

        return $result ?: null;
    }

    public function findByActive(string $table): ?array
    {
        $table = self::PRE_TABLE . $table;

        $sql = "SELECT * FROM {$table} WHERE status = 'ativo'";

        $statement = $this->connection->query($sql);

        return $statement->fetchAll();
    }

    public function findByDeactive(string $table): ?array
    {
        $table = self::PRE_TABLE . $table;

        $sql = "SELECT * FROM {$table} WHERE status = 'inativo'";

        $statement = $this->connection->query($sql);

        return $statement->fetchAll();
    }

    //updates
    public function saveChanges(string $table, int $id, array $dados): bool
    {
        $primaryKey = 'id_' . $table;
        $table = self::PRE_TABLE . $table;

        $fields = [];

        foreach (array_keys($dados) as $column) {
            $fields[] = "{$column} = :{$column}";
        }

        $set = implode(', ', $fields);

        $sql = "UPDATE {$table}
                SET {$set}
                WHERE {$primaryKey} = :id";

        $statement = $this->connection->prepare($sql);

        $dados['id'] = $id;

        return $statement->execute($dados);
    }

    public function deactivate(string $table, int $id): bool
    {
        $primaryKey = 'id_' . $table;
        $table = self::PRE_TABLE . $table;

        $sql = "UPDATE {$table}
                SET status = :status
                WHERE {$primaryKey} = :id";

        $statement = $this->connection->prepare($sql);

        return $statement->execute([
            'status' => 'inativo',
            'id' => $id
        ]);
    }
}