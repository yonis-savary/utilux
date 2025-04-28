<?php

namespace YonisSavary\DBWand\CLI\Commands;

use PDO;
use PDOException;
use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class Update extends Command
{
    public function name(): string
    {
        return "update";
    }

    public function help(): ?string
    {
        return 'Perform an update query (you can copy-paste a query in the console directly)';
    }

    public function execute(array $argv = [], Context &$context): bool
    {
        if (strtolower($argv[0] ?? '') !== 'update')
            array_unshift($argv, 'UPDATE');

        $command = join(' ', $argv);
        try {
            $statement = $context->database->query($command, PDO::FETCH_ASSOC);

            $currentSelection = $statement->fetchAll();
            $context->output->info("Affected rows : " . count($currentSelection));
            return true;
        } catch (PDOException $exception) {
            return $this->failWithException($exception);
        }
    }
}
