<?php 

namespace YonisSavary\DBWand\CLI\Commands;

use PDO;
use PDOException;
use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class Select extends Command
{
    const CONTEXT_LAST_QUERY = "last-select-query";

    public function name(): string
    {
        return "select";
    }


    public function help(): ?string
    {
        return 'Perform a select query (you can copy-paste a query in the console directly, "dbwand" table can be used to fetch from dataset)';
    }

    public function execute(array $argv = [], Context &$context): bool
    {
        if (strtolower($argv[0] ?? '') !== 'select')
            array_unshift($argv, 'SELECT');

        $currentData = &$context->currentData;

        $command = join(' ', $argv);
        try
        {
            if (str_contains(strtolower($command), "from dbwand"))
            {
                $context->output->notice("Fetching data from dataset");
                $valuesTable = $context->utils->datasetToSelectValuesExpression($context->currentData);
                $command = preg_replace("/FROM dbwand/i", "FROM $valuesTable", $command);
            }
            else 
            {
                $context->setProperty(self::CONTEXT_LAST_QUERY, $command);
            }

            $statement = $context->database->query($command, PDO::FETCH_ASSOC);

            if (count($currentData))
                Backup::add($context);

            $currentData = $statement->fetchAll();
            $context->output->notice("Fetched " . count($currentData) . " rows");

            return true;
        }
        catch (PDOException $exception)
        {
            return $this->failWithException($exception);
        }
    }
}