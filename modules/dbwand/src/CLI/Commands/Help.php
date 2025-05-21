<?php 

namespace YonisSavary\DBWand\CLI\Commands;

use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class Help extends Command
{
    public function name(): string
    {
        return "help";
    }

    public function help(): ?string
    {
        return "Show this help message";
    }

    public function execute(array $argv, Context &$context): bool
    {
        $context->output->info("List of commands:");

        $commands = [];

        foreach ($context->terminal->getCommands() as $name => $command )
        {
            $commands[] = [
                'name' => $name,
                'description' => $command->help()
            ];
        }

        $this->displayTable($commands, $context);
        return true;
    }
}