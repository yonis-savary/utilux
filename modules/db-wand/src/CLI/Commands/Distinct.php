<?php 

namespace YonisSavary\DBWand\CLI\Commands;

use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class Distinct extends Command
{
    public function name(): string
    {
        return "distinct";
    }

    public function help(): ?string
    {
        return "Retrieve distinct values from a column";
    }

    public function execute(array $argv = [], Context &$context): bool
    {
        $column = $argv[0] ?? null;

        if (!$column)
            return $this->failWithReason("This command need a column name");

        $data = $context->currentData;
        if (!count($data))
            return $this->failWithReason("No data to distinct from");

        if (!array_key_exists($column, $data[0]))
            return $this->failWithReason("$column is not a field in your dataset");

        $distinctMap = [];

        foreach ($data as &$row)
        {
            $key = $row[$column] ?? "<null>";
            $distinctMap[$key] ??= [];
            $distinctMap[$key][] = &$row;
        }

        $keys = array_keys($distinctMap);
        sort($keys);

        foreach ($keys as $key)
            $context->output->info(" - $key (" . count($distinctMap[$key]) . ' elements)' );

        $context->output->info('');
        $context->output->info('Displayed ' . count($keys) . ' distinct elements');
        return true;
    }
}