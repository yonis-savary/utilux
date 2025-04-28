<?php 

namespace YonisSavary\DBWand\CLI\Commands;

use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class Describe extends Command
{
    public function name(): string
    {
        return "describe";
    }

    public function help(): ?string
    {
        return "List columns in your dataset";
    }

    public function execute(array $argv = [], Context &$context): bool
    {
        $data = $context->currentData;

        if (!count($data))
            return $this->failWithReason("No data to describe");

        $columns = array_keys($data[0]);
        if (!count($columns))
        {
            $context->output->warning("No column to describe");
            return true;
        }

        foreach ($columns as $column)
        {
            $context->output->info(" - $column");
        }

        $context->output->info('');
        $context->output->info("Described ".count($columns)." fields");
        return true;
    }
}