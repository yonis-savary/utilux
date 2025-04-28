<?php 

namespace YonisSavary\DBWand;

use InvalidArgumentException;
use PDO;
use RuntimeException;
use YonisSavary\DBWand\Database\Utils\DatabaseUtils;
use YonisSavary\DBWand\Database\Utils\MySQL;
use YonisSavary\DBWand\Database\Utils\Postgres;
use YonisSavary\DBWand\Database\Utils\SQLite;

enum ConnectionType: string
{
    public static function fromPDO(PDO $connection): self
    {
        $driver = $connection->getAttribute(PDO::ATTR_DRIVER_NAME);
        return self::fromString($driver);
    }

    public static function fromString(string $pdoDriver): self
    {
        foreach (self::cases() as $case)
        {
            if ($case->value === $pdoDriver)
                return $case;
        }

        $supported = join(",", array_map(fn($x) => $x->name, self::cases()));
        throw new InvalidArgumentException("Unsupported DB Driver : $pdoDriver (supported are : $supported");
    }

    public static function getDatabaseUtils(PDO $database): DatabaseUtils
    {
        $driver = self::fromPDO($database);
        if (is_string($driver))
            $driver = self::fromString($driver);

        switch ($driver)
        {
            case self::MYSQL:
                return new MySQL($database);
            case self::POSTGRES:
                return new Postgres($database);
            case self::SQLITE:
                return new SQLite($database);
            default:
                throw new RuntimeException("No DatabaseUtils class were found for driver [" . $driver->name . "]");
        }
    }

    case MYSQL = 'mysql';
    case POSTGRES = 'pgsql';
    case SQLITE = 'sqlite';
}