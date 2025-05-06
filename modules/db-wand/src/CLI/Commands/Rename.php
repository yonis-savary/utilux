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

    public function execute(array $argv, Context &$context): bool
    {
        $dataset = &$context->dataset;

        if (!count($dataset))
            return $this->failWithReason("No data to process");

        $oldName = $argv[0] ?? null;
        $newName = $argv[1] ?? null;

        if (!($oldName || $newName))
            return $this->failWithReason('Usage: rename <old-column-name> <new-column-name>');

        if (!array_key_exists($oldName, $dataset[0]))
            return $this->failWithReason("No column [$oldName] found");

        if (array_key_exists($newName, $dataset[0]))
            return $this->failWithReason("Column [$newName] already exists");

        Backup::add($context);

        foreach ($dataset as &$row)
        {
            $newValue = [];
            foreach ($row as $header => $value)
                $newValue[ $header === $oldName ? $newName : $header ] = $value;

            $row = $newValue;
        }

        return true;
    }
}