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

    public function execute(array $argv, Context &$context): bool
    {
        $dataset = &$context->dataset;

        if (!count($dataset))
            return $this->failWithReason('No data to print');

        $columns = array_keys($dataset[0]);
        if (!count($columns))
            return $this->failWithReason("No column to print");

        $column = $argv[0] ?? null;
        if ($column || count($columns)==1)
        {
            $column ??= $columns[1];
            $context->output->info($context->utils->datasetToValuesExpression([array_map(fn($x) => $x[$column], $context->dataset)]));
        }
        else 
        {
            $context->output->info($context->utils->datasetToValuesExpression($context->dataset));
        }

        return true;
    }
}