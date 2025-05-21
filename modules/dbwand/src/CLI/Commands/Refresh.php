<?php

namespace YonisSavary\DBWand\CLI\Commands;

use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class Refresh extends Command
{
    public function name(): string
    {
        return "refresh";
    }

    public function help(): ?string
    {
        return 'Refresh data set with last used select query';
    }

    public function execute(array $argv, Context &$context): bool
    {
        if (! $lastQuery = $context->getProperty(Select::CONTEXT_LAST_QUERY, false))
            return false;

        $context->output->info("Fetching data with");
        $context->output->info($lastQuery);

        /** @var Select $select */
        $select = $context->terminal->getCommand('select');
        $select->execute(explode(' ', $lastQuery), $context);
        return true;
    }
}
