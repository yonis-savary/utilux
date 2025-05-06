<?php

namespace YonisSavary\DBWand\Tests\CLI;

use PDO;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use YonisSavary\DBWand\CLI\Terminal;
use YonisSavary\DBWand\ConnectionType;
use YonisSavary\DBWand\Context;
use YonisSavary\DBWand\Tests\DBWandTestCase;
use YonisSavary\DBWand\Tests\TestUtils;

abstract class CLITestCase extends DBWandTestCase
{
    /**
     * @return PDO[]
     */
    public static function allDatabases(): array
    {
        $dbList = self::allConnections();

        $pairs = [];
        foreach ($dbList as &$connection)
            $pairs[] = [
                new Terminal(new ArrayLogger, $connection),
                ConnectionType::getDatabaseUtils($connection),
                $connection,
            ];

        return $pairs;
    }

    public function expectCountFromScript(int $count, string $script, Terminal $terminal): Context
    {
        $context = $terminal->handleScript($script);
        $this->assertCount($count, $context->dataset);
        return $context;
    }
}
