<?php 

namespace YonisSavary\DBWand\CLI\Commands;

use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class Rename extends Command
{
    public function name(): string
    {
        return "rename";
    }

    public function help(): ?string
    {
        return 'Rename a column in place in your data set (ex "rename label name")';
    }

    public function execute(array $argv = [], Context &$context): bool
    {
        $currentData = &$context->currentData;

        if (!count($currentData))
            return $this->failWithReason("No data to process");

        $oldName = $argv[0] ?? null;
        $newName = $argv[1] ?? null;

        if (!($oldName || $newName))
            return $this->failWithReason('Usage: rename <old-column-name> <new-column-name>');

        if (!array_key_exists($oldName, $currentData[0]))
            return $this->failWithReason("No column [$oldName] found");

        if (array_key_exists($newName, $currentData[0]))
            return $this->failWithReason("Column [$newName] already exists");

        Backup::add($context);

        foreach ($currentData as &$row)
        {
            $newValue = [];
            foreach ($row as $header => $value)
                $newValue[ $header === $oldName ? $newName : $header ] = $value;

            $row = $newValue;
        }

        return true;
    }
}