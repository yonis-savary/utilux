<?php 

namespace YonisSavary\DBWand\CLI\Commands;

use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class ExitCLI extends Command
{
    public function name(): string
    {
        return "exit";
    }

    public function help(): ?string
    {
        return "Exit dbwand";
    }

    public function execute(array $argv=[], Context &$context): bool
    {
        $context->output->notice("Bye!");
        exit(0);
    }
}