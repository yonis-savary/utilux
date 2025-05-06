<?php 

namespace YonisSavary\DBWand\CLI\Commands;

use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class Save extends Command
{
    const SAVE_SLOTS_NAME = "data-save";

    public function name(): string
    {
        return "save";
    }

    public function help(): ?string
    {
        return 'Save current data to save slot (ex: "save tmp-contact")';
    }

    public function execute(array $argv, Context &$context): bool
    {
        if (! $name = $argv[0] ?? false)
            return $this->failWithReason("This command needs a slot name as argument");

        $slots = &$context->getReference(self::SAVE_SLOTS_NAME, []);
        $slots[$name] = $context->dataset;
        $context->output->notice(count($context->dataset) . " rows saved into [$name]");
        return true;
    }
}