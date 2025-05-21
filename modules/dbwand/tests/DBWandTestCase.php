<?php 

namespace YonisSavary\DBWand\Tests;

use PDO;
use PHPUnit\Framework\TestCase;
use YonisSavary\DBWand\ConnectionType;
use YonisSavary\DBWand\Database\Utils\DatabaseUtils;

abstract class DBWandTestCase extends TestCase
{ 
    /**
     * @return PDO[]
     */
    public static function allConnections(): array
    {
        return [
            new PDO("pgsql:host=localhost;port=44001;dbname=postgres", "root", "root")
        ];
    }

    /**
     * @return DatabaseUtils[]
     */
    public static function allDatabaseUtils(): array
    {
        return array_map(
            fn($connection) => [ConnectionType::getDatabaseUtils($connection)],
            self::allConnections()
        );
    }
}