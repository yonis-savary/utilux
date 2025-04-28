<?php 

namespace YonisSavary\DBWand;

use PDO;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use YonisSavary\DBWand\CLI\Terminal;
use YonisSavary\DBWand\Configuration\Configuration;
use YonisSavary\DBWand\ConnectionType;
use YonisSavary\DBWand\Database\Utils\DatabaseUtils;
use YonisSavary\DBWand\DBWand;

class Context
{
    public LoggerInterface $output;

    public PDO $database;
    public DatabaseUtils $utils;
    public DBWand $wand;
    public Terminal $terminal;
    public Configuration $configuration;

    public static function new(PDO $connection, ?LoggerInterface $logger = null): self 
    {
        $logger ??= new NullLogger;

        $context = new Context();
        $context->changeConnection($connection);
        $context->output = $logger;
        return $context;
    }

    public function changeConnection(PDO $newConnection)
    {
        $this->database = $newConnection;
        $this->wand = DBWand::fromContext($this);
        $this->utils = ConnectionType::getDatabaseUtils($this->database);
    }

    public array $promptHistory = [];
    public array $currentData = [];

    public array $properties = [];

    public function setProperty(string $name, $value)
    {
        $this->properties[$name] = $value;
    }

    public function getProperty(string $name, mixed $default = null)
    {
        return $this->properties[$name] ?? $default;
    }

    public function &getReference(string $name, mixed $starterValue)
    {
        if (!array_key_exists($name, $this->properties))
            $this->properties[$name] = $starterValue;

        return $this->properties[$name];
    }
}