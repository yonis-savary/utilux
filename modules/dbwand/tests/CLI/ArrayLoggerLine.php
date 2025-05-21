<?php 

namespace YonisSavary\DBWand\Tests\CLI;

readonly class ArrayLoggerLine
{
    public function __construct(
        public readonly string $level,
        public readonly string $rawMessage,
        public readonly array $context,
        public readonly string $message
    ){}
}