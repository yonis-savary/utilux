<?php

namespace YonisSavary\DBWand\Database\Classes;

readonly class ForeignKey
{
    const ON_DELETE_CASCADE = 'cascade';
    const ON_DELETE_RESTRAINT = 'restraint';
    const ON_DELETE_SET_NULL = 'set-null';

    public function __construct(
        public readonly Field $source,
        public readonly Field $target,
    ){}

    public static function fromStrings(
        string $sourceTable,
        string $sourceField,
        string $targetTable,
        string $targetField,
    ) {
        return new self(
            new Field($sourceTable, $sourceField),
            new Field($targetTable, $targetField)
        );
    }
}