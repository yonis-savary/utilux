<?php 

namespace YonisSavary\DBWand\CLI\Commands;

use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class ListTables extends Command
{
    public function name(): string
    {
        return "list-tables";
    }

    public function help(): ?string
    {
        return "List tables in your database";
    }

    public function execute(array $argv = [], Context &$context): bool
    {
        $tables = $context->utils->listTables();

        if (!(in_array("-l", $argv) || in_array("--list", $argv)))
        {
            $tables = join("    ", $tables);
            $context->output->info($tables);
        }
        else 
        {
            foreach ($tables as $table)
                $context->output->info(" - ". $table);
        }

        return true;
    }
}