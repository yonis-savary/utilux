<?php 

namespace YonisSavary\DBWand\CLI\Commands;

use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class Only extends Command
{
    public function name(): string
    {
        return "only";
    }

    public function help(): ?string
    {
        return 'Keep only specified columns (ex: "only id label deleted_at")';
    }

    public function execute(array $argv = [], Context &$context): bool
    {
        $currentData = &$context->currentData;

        if (!count($currentData))
            return $this->failWithReason('No data to filter');

        $allKeys = array_keys($currentData[0]);
        $keyToDelete = array_diff($allKeys, $argv);

        Backup::add($context);

        foreach ($currentData as &$row)
        {
            foreach ($keyToDelete as $key)
                unset($row[$key]);
        }

        return true;
    }
}