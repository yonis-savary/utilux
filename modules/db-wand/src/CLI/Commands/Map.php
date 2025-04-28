<?php 

namespace YonisSavary\DBWand\CLI\Commands;

use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class Map extends Command
{
    public function name(): string
    {
        return "map";
    }

    public function help(): ?string
    {
        return 'Rename every field with given parameters (ex: "map id label code")';
    }

    public function execute(array $argv = [], Context &$context): bool
    {
        $newNames = $argv;
        $oldNames = array_keys($context->currentData[0] ?? []);

        if (!count($newNames))
            return $this->failWithReason("No new column names given");

        if (!count($oldNames))
            return $this->failWithReason("No current column names found");

        if (count($newNames) !== count($oldNames))
            return $this->failWithReason("Invalid column count, expected " . count($oldNames) . ' found ' . count($newNames));

        $currentData = &$context->currentData;
        $nameMap = array_combine($oldNames, $newNames);

        Backup::add($context);

        foreach ($currentData as &$row)
        {
            $newValue = [];
            foreach ($row as $field => $value)
                $newValue[ $nameMap[$field] ] = $value;

            $row = $newValue;
        }
        return true;
    }
}