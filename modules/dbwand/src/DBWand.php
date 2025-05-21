<?php

namespace YonisSavary\DBWand;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use PDO;
use PDOException;
use Psr\Log\LoggerInterface;
use Throwable;
use YonisSavary\DBWand\Context;
use YonisSavary\DBWand\Database\Classes\Field;
use YonisSavary\DBWand\Database\Classes\ForeignKey;
use YonisSavary\DBWand\Database\Classes\UpdateTree;

class DBWand
{
    protected Context $context;

    public static function fromContext(Context $context): self 
    {
        return new self($context);
    }

    public function __construct(?Context $context = null, ?PDO $connection = null, ?LoggerInterface $logger = null)
    {
        if (!$context && !$connection)
            throw new InvalidArgumentException('Either context or connection is needed to instanciate a DBWand instance');

        $this->context = $context ?? Context::new($connection, $logger);
    }

    public function withTransaction(callable|\Closure $callback): Throwable|true
    {
        $utils = $this->context->utils;
        $logger = $this->context->output;

        $logger->info("Start transaction");
        $utils->startTransaction();

        try {
            ($callback)();

            $logger->info("Successful transaction");
            $utils->commitTransaction();

            return true;
        } catch (PDOException $exception) {
            $logger->error("Got an error while processing transaction");
            $logger->error($exception->getMessage());

            $utils->rollbackTransaction();

            return $exception;
        }
    }

    public function updateColumnWithMap(string $table, string $column, array $map) {}

    public function createMissingRelationFor(string $table, string $column, array $acceptedColumnName, string $onDelete = ForeignKey::ON_DELETE_RESTRAINT)
    {
        $targetField = new Field($table, $column);
        $utils = $this->context->utils;

        $existingForeignKeysRaw = $utils->getFieldsPointingTo($targetField);
        $refinedExistingForeignKeys = [];

        foreach ($existingForeignKeysRaw as $relation) {
            $table = $relation->table;
            $field = $relation->field;

            $refinedExistingForeignKeys[$table] ??= [];
            $refinedExistingForeignKeys[$table][] = $field;
        }

        $missingColumns = array_values(array_filter(
            $utils->listFields(),
            fn(Field $field) =>
            in_array($field->field, $acceptedColumnName) &&
                (!array_key_exists($field->field, $refinedExistingForeignKeys[$field->table] ?? []))
        ));

        foreach ($missingColumns as $field)
            $utils->createForeignKeyConstraint($field, $targetField, $onDelete);
    }

    public function getUpdateTree(Field $base): UpdateTree
    {
        $tree = new UpdateTree($base);
        foreach ($this->context->utils->getFieldsPointingTo($base) as $field)
            $tree->pushSubTree($this->getUpdateTree($field));

        return $tree;
    }
}
