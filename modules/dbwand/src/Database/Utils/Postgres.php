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
        $statement = $this->database->query(
            "SELECT
                c.relname AS table_name,
                a.attname AS column_name
            FROM pg_catalog.pg_attribute a
            JOIN pg_catalog.pg_class c ON a.attrelid = c.oid
            JOIN pg_catalog.pg_namespace n ON c.relnamespace = n.oid
            WHERE a.attnum > 0 -- ignore les colonnes système
            AND NOT a.attisdropped -- ignore les colonnes supprimées
            AND c.relkind IN ('r', 'v', 'm', 'f', 'p') -- r = table, v = vue, m = vue matérialisée, f = table étrangère, p = table partitionnée
            AND n.nspname NOT IN ('information_schema', 'pg_catalog')
            ORDER BY table_name, a.attnum;
        ");

        $fields = array_map(
            fn($row) => new Field($row['table_name'], $row['column_name']), 
            $statement->fetchAll(PDO::FETCH_ASSOC)
        );

        return $fields;
    }

    /** @return string[] */
    public function listTables(): array
    {
        $statement = $this->database->query(
            "SELECT c.relname AS table_name
            FROM pg_catalog.pg_class c
            JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace
            WHERE c.relkind IN ('r', 'v', 'm', 'f', 'p')
            AND n.nspname NOT IN ('information_schema', 'pg_catalog')
            ORDER BY table_name
        ");
        $tables = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($x) => $x['table_name'], $tables);
    }

    /** @return ForeignKey[] */
    public function listForeignKeyConstraints(): array
    {
        $statement = $this->database->query(
            "SELECT * FROM (
            SELECT
                pgc.contype as constraint_type,
                ccu.table_schema AS table_schema,
                kcu.table_name as table_name,
                CASE WHEN (pgc.contype = 'f') THEN kcu.COLUMN_NAME ELSE ccu.COLUMN_NAME END as column_name,
                CASE WHEN (pgc.contype = 'f') THEN ccu.TABLE_NAME ELSE (null) END as reference_table,
                CASE WHEN (pgc.contype = 'f') THEN ccu.COLUMN_NAME ELSE (null) END as reference_col
            FROM
                pg_constraint AS pgc
                JOIN pg_namespace nsp ON nsp.oid = pgc.connamespace
                JOIN pg_class cls ON pgc.conrelid = cls.oid
                JOIN information_schema.key_column_usage kcu ON kcu.constraint_name = pgc.conname
                LEFT JOIN information_schema.constraint_column_usage ccu
                            ON pgc.conname = ccu.CONSTRAINT_NAME
                        AND nsp.nspname = ccu.CONSTRAINT_SCHEMA
                WHERE pgc.contype IN ('f')
                AND nsp.nspname NOT IN ('information_schema', 'pg_catalog')
            )   as foo
            ORDER BY table_name desc;
        "
        );

        return array_map(
            fn($row) => new ForeignKey(new Field($row['table_name'], $row['column_name']), new Field($row['reference_table'], $row['reference_col'])),
            $statement->fetchAll(PDO::FETCH_ASSOC)
        );
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

    public function escapeTableName(string $table): string
    {
        return '"' . $table . '"';
    }
}