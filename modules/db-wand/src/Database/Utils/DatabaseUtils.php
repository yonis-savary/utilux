<?php 

namespace YonisSavary\DBWand\Database\Utils;

use PDO;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Throwable;
use YonisSavary\DBWand\Database\Classes\Field;
use YonisSavary\DBWand\Database\Classes\ForeignKey;
use YonisSavary\DBWand\Database\Classes\UniqueConstraint;

abstract class DatabaseUtils
{
    protected LoggerInterface $logger;

    public function __construct(
        protected PDO $database,
        ?LoggerInterface $logger=null
    ){
        $this->logger = $logger ?? new NullLogger();
    }

    /** @return Field[] */
    abstract public function listFields(): array;

    /** @return string[] */
    abstract public function listTables(): array;

    /** @return ForeignKey[] */
    abstract public function listForeignKeyConstraints(): array;

    /** @return UniqueConstraint[] */
    abstract public function listUniqueConstraints(): array;

    abstract public function startTransaction(): void;
    abstract public function commitTransaction(): void;
    abstract public function rollbackTransaction(): void;

    public function transaction(callable $function): mixed
    {
        try
        {
            $this->startTransaction();
            $results = ($function)();
            $this->commitTransaction();
            return $results;
        }
        catch (Throwable $err)
        {
            $this->rollbackTransaction();
            return $err;
        }
    }

    abstract public function assertTableExists(string $table): void;
    abstract public function assertColumnExists(string $table, string $column): void;

    abstract public function createForeignKeyConstraint(Field $source, Field $target, string $onDelete=ForeignKey::ON_DELETE_RESTRAINT);

    /**
     * @return Field[]
     */
    public function getFieldsPointingTo(Field $field): array
    {
        $table = $field->table;
        $field = $field->field;

        return array_values(array_filter(
            $this->listUniqueConstraints(),
            fn(ForeignKey $constraint) =>
                $constraint->target->table === $table &&
                $constraint->target->field === $field
        ));
    }

    abstract public function datasetToSelectValuesExpression(array $data): string;
    abstract public function datasetToValuesExpression(array $data): string;

}