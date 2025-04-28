<?php

namespace YonisSavary\DBWand\CLI\Commands;

use PDO;
use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\CLI\ConnectionFinder;
use YonisSavary\DBWand\Context;

class Connect extends Command
{
    public function name(): string
    {
        return "connect";
    }

    public function help(): ?string
    {
        return "Connect to another connection in your .env file";
    }

    public function execute(array $argv = [], Context &$context): bool
    {
        if (! $connectionName = $argv[0] ?? false)
            return $this->failWithReason("This command needs a connection name");


        $connections = $context->configuration->getJsonConfiguration()['connections'] ?? [];
        if (! $url = $connections[$connectionName] ?? false)
            return $this->failWithReason("No $connectionName found in your config file");

        $connectionFinder = new ConnectionFinder($context);
        $pdoOrException = $connectionFinder->getConnectionFromUrl($url);
        if ($pdoOrException instanceof PDO) {
            $context->output->notice("Connected from config file (connection $connectionName)");
            $context->changeConnection($pdoOrException);
            return true;
        }

        return $this->failWithException($pdoOrException);
    }
}
