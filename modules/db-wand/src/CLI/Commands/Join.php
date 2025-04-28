<?php 

namespace YonisSavary\DBWand\CLI\Commands;

use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class Join extends Command
{
    public function name(): string
    {
        return "join";
    }

    public function help(): ?string
    {
        return "Prints a SQL representation of your data";
    }

    public function execute(array $argv = [], Context &$context): bool
    {
        $currentData = &$context->currentData;

        if (!count($currentData))
            return $this->failWithReason('No data to print');

        $columns = array_keys($currentData[0]);
        if (!count($columns))
            return $this->failWithReason("No column to print");

        $column = $argv[0] ?? null;
        if ($column || count($columns)==1)
        {
            $column ??= $columns[1];
            $context->output->info($context->utils->datasetToValuesExpression([array_map(fn($x) => $x[$column], $context->currentData)]));
        }
        else 
        {
            $context->output->info($context->utils->datasetToValuesExpression($context->currentData));
        }

        return true;
    }
}