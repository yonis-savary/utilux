<?php

namespace YonisSavary\DBWand\CLI\Commands;

use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class Backup extends Command
{
    const CONTEXT_DATA_STACK = 'backup-stack';

    public static function add(Context &$context)
    {
        $data = $context->currentData;
        $stack = &$context->getReference(self::CONTEXT_DATA_STACK, []);
        if (count($stack) > 10)
            array_shift($stack);

        array_push($stack, $data);
    }

    public function name(): string
    {
        return "backup";
    }

    public function help(): ?string
    {
        return "Backup previous set of data before editing (max 10 backups)";
    }

    public function execute(array $argv = [], Context &$context): bool
    {
        $stack = &$context->getReference(self::CONTEXT_DATA_STACK, []);

        if (!count($stack)) {
            $context->output->notice("No data in backup stack");
            return false;
        }

        $old = array_pop($stack);
        $context->currentData = $old;
        $context->output->notice("Recovered previous data");

        return true;
    }
}
