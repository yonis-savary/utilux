<?php

namespace YonisSavary\DBWand\Database\Classes;

readonly class Field
{
    public function __construct(
        public readonly string $table,
        public readonly string $field,
    ){}
}