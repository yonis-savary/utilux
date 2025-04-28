<?php 

namespace YonisSavary\DBWand\CLI\Commands;

use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class Dump extends Command
{
    public function name(): string
    {
        return "dump";
    }

    public function help(): ?string
    {
        return "Generate an INSERT query from fetched data";
    }

    public function execute(array $argv = [], Context &$context): bool
    {
        $data = $context->currentData;
        if (!count($data))
            return $this->failWithReason("No data to dump");

        $lastQuery = $context->getProperty(Select::CONTEXT_LAST_QUERY);
        if (!$lastQuery)
            return $this->failWithReason("Could not retrieve last select query");

        $table = [];
        preg_match("/FROM (\w+)/i", $lastQuery, $table);
        $table = $table[1] ?? 0;

        $columns = array_keys($data[0]);
        $columns = $context->utils->datasetToValuesExpression([$columns]);
        $values = $context->utils->datasetToValuesExpression($context->currentData);

        $context->output->info("INSERT INTO $table $columns VALUES $values");
        return true;
    }
}