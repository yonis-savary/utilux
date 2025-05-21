<?php 

namespace YonisSavary\DBWand\CLI\Commands;

use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class Erase extends Command
{
    public function name(): string
    {
        return "erase";
    }

    public function help(): ?string
    {
        return "Erase a save slot";
    }

    public function execute(array $argv, Context &$context): bool
    {
        $name = $argv[0] ?? null;
        $slots = &$context->getReference(Save::SAVE_SLOTS_NAME, []);
        if (array_key_exists($name, $slots))
            unset($slots[$name]);
        
        return 0;
    }
}