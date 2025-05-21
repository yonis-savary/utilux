<?php 

namespace YonisSavary\DBWand\CLI\Commands;

use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class History extends Command
{
    public function name(): string
    {
        return "history";
    }

    public function help(): ?string
    {
        return "Show typed commands in this session (useful for scripting)";
    }

    public function execute(array $argv, Context &$context): bool
    {
        $context->output->info("# --------------------------------------------");
        $context->output->info("# History");
        $context->output->info("# --------------------------------------------");
        foreach ($context->promptHistory as $prompt)
        {
            $context->output->info($prompt);
        }
        $context->output->info("# --------------------------------------------");
        return true;
    }
}