<?php

namespace YonisSavary\DBWand\CLI\Commands;

use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class Load extends Command
{
    const SAVE_SLOTS_NAME = Save::SAVE_SLOTS_NAME;

    public function name(): string
    {
        return "load";
    }

    public function help(): ?string
    {
        return 'Load data from save slot (ex: "load tmp-contact")';
    }

    public function execute(array $argv = [], Context &$context): bool
    {
        if (! $name = $argv[0] ?? false)
            return $this->failWithReason("This command needs a slot name as argument");

        $slots = &$context->getReference(self::SAVE_SLOTS_NAME, []);
        if (! array_key_exists($name, $slots))
            return $this->failWithReason("No slot named [$name] exists");

        $context->currentData = $slots[$name];
        $context->output->notice("Loaded " . count($context->currentData) . " rows from $name");
        return true;
    }
}
