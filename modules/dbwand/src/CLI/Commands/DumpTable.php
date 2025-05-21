<?php 

namespace YonisSavary\DBWand\CLI\Commands;

use Throwable;
use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class DumpTable extends Command
{
    public function name(): string
    {
        return "dump-table";
    }

    public function help(): ?string
    {
        return "Display a SQL Insert query of a table";
    }

    public function execute(array $argv, Context &$context): bool
    {
        if (! $tableName = $argv[0] ?? false)
            return $this->failWithReason("A table name must be specified");

        $name = uniqid("slot-");
        $terminal = $context->terminal;
        $terminal->handlePrompt("save $name", $context);

        try
        {
            $terminal->handlePrompt("select * from " . $context->utils->escapeTableName($tableName), $context);
            $terminal->handlePrompt("dump", $context);
        }
        catch (Throwable $err)
        {
            $terminal->handlePrompt("load $name", $context);
            return $this->failWithException($err);
        }

        $terminal->handlePrompt("load $name", $context);
        return true;
    }
}