<?php 

namespace YonisSavary\DBWand\Database\Classes;

readonly class UniqueConstraint
{
    public function __construct(
        public readonly string $constraintName,
        public readonly string $table,
        public readonly array $columns
    ){}
}