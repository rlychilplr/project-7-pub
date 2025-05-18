<?php

require_once __DIR__ . "/../core/Model.php";

// from previous project (sander)
// slightly modified to work with a db connection in a class instead of direct access
// also added the findby method

abstract class BaseORM extends Model
{
    protected $pdo;
    protected $table;
    protected $primaryKey;

    public function __construct()
    {
        $this->pdo = self::getDB();
    }

    /**
     * @param int|null $id
     * @param string $data
     */
    public function save($data, $id = null): int|string
    {
        try {
            if ($id === null) {
                return $this->insert($data);
            } else {
                return $this->update($data, $id);
            }
        } catch (PDOException $e) {
            throw new Exception("Save operation failed: " . $e->getMessage());
        }
    }

    /**
     * @param string $data
     */
    protected function insert($data): string
    {
        $columns = implode(", ", array_keys($data));
        $values = implode(", ", array_fill(0, count($data), "?"));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($values)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));

        return $this->pdo->lastInsertId();
    }

    /**
     * @param int|null $id
     * @param array $data
     */
    protected function update($data, $id): int
    {
        $set = [];
        foreach ($data as $column => $value) {
            $set[] = "$column = ?";
        }
        $set = implode(", ", $set);

        $sql = "UPDATE {$this->table} SET $set WHERE {$this->primaryKey} = ?";

        $stmt = $this->pdo->prepare($sql);
        $values = array_values($data);
        $values[] = $id;
        $stmt->execute($values);

        return $id;
    }

    /**
     * @param int|null $id
     */
    public function delete($id): bool
    {
        try {
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Delete operation failed: " . $e->getMessage());
        }
    }

    public function getId(): string
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * @param int|null $id
     */
    public function findById($id): array|bool
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Find operation failed: " . $e->getMessage());
        }
    }

    public function findAll(): array
    {
        try {
            $sql = "SELECT * FROM {$this->table}";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                "FindAll operation failed: " . $e->getMessage()
            );
        }
    }

    /**
     * @param array $conditions
     * @param array $orderBy
     */
    protected function findBy($conditions, $orderBy = []): array
    {
        $where = [];
        $params = [];

        foreach ($conditions as $field => $value) {
            $where[] = "$field = ?";
            $params[] = $value;
        }

        $sql = "SELECT * FROM {$this->table}";

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        if (!empty($orderBy)) {
            $orderClauses = [];
            foreach ($orderBy as $field => $direction) {
                $orderClauses[] = "$field $direction";
            }
            $sql .= " ORDER BY " . implode(", ", $orderClauses);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
