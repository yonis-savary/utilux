<?php

namespace YonisSavary\DBWand\Database\Utils;

use Exception;
use PDO;
use PDOException;
use YonisSavary\DBWand\Database\Classes\Field;
use YonisSavary\DBWand\Database\Classes\ForeignKey;

class Postgres extends DatabaseUtils
{
    /** @return Field[] */
    public function listFields(): array
    {
        return [];
    }

    /** @return string[] */
    public function listTables(): array
    {
        $statement = $this->database->query("SELECT tablename FROM pg_catalog.pg_tables");
        $tables = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($x) => $x['tablename'], $tables);
    }

    /** @return ForeignKey[] */
    public function listForeignKeyConstraints(): array
    {
        return [];
    }

    /** @return UniqueConstraint[] */
    public function listUniqueConstraints(): array
    {
        return [];
    }

    public function startTransaction(): void
    {
        $this->database->exec("START TRANSACTION");
    }
    public function commitTransaction(): void
    {
        $this->database->exec("COMMIT");
    }
    public function rollbackTransaction(): void
    {
        $this->database->exec("ROLLBACK");
    }

    public function assertTableExists(string $table): void
    {
        try
        {
            $this->database->query("SELECT 1 FROM \"$table\"");
        }
        catch (PDOException $_)
        {
            throw new Exception("Could not assert that table \"$table\" exists");
        }
    }

    public function assertColumnExists(string $table, string $column): void
    {
        try
        {
            $this->database->query("SELECT \"$column\" FROM \"$table\" LIMIT 1");
        }
        catch (PDOException $_)
        {
            throw new Exception("Could not assert that table \"$table\" exists");
        }
    }

    public function createForeignKeyConstraint(Field $source, Field $target, string $onDelete=ForeignKey::ON_DELETE_RESTRAINT)
    {
        return;
    }

    public function datasetToSelectValuesExpression(array $data): string
    {
        if (!count($data))
            return "(VALUES (NULL)) dbwand(_)";

        $columns = array_keys($data[0]);
        if (!count($columns))
            return "(VALUES (NULL)) dbwand(_)";

        $sqlColumns = [];
        foreach ($columns as $column)
            $sqlColumns[] = '"'. $column .'"';

        return "(VALUES ". $this->datasetToValuesExpression($data) .") dbwand(".join(',', $sqlColumns).")";
    }

    public function datasetToValuesExpression(array $data): string
    {
        if (!count($data))
            return "(NULL)";

        $columns = array_keys($data[0]);
        if (!count($columns))
            return "(NULL)";

        $sqlValues = [];
        foreach ($data as $row)
        {
            $row = array_values($row);
            foreach ($row as &$value)
            {
                if ($value === null)
                    $value = 'NULL';
                else if (is_int($value) || is_float($value))
                    $value = "$value";
                else if ($value === true || $value === false)
                    $value = $value ? "TRUE": "FALSE";
                else
                    $value = "'". str_replace("'", "''", $value) ."'";
            }

            $sqlValues[] = "(".join(",", $row).")";
        }

        return join(',', $sqlValues);
    }
}