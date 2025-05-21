<?php 

namespace YonisSavary\DBWand\CLI\Commands;

use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class Remove extends Command
{
    public function name(): string
    {
        return "remove";
    }

    public function help(): ?string
    {
        return 'Remove specified columns (ex: "remove id name code")';
    }

    public function execute(array $argv, Context &$context): bool
    {
        Backup::add($context);

        foreach ($context->dataset as &$row)
        {
            foreach ($argv as $field)
                unset($row[$field]);
        }
        return true;
    }
}