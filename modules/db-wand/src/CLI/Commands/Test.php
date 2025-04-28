<?php 

namespace YonisSavary\DBWand\CLI\Commands;

use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class Test extends Command
{
    public function name(): string
    {
        return "test";
    }


    public function help(): ?string
    {
        return 'Print a test message';
    }

    public function execute(array $argv = [], Context &$context): bool
    {
        $context->output->info("I'm a test !");
        return true;
    }
}