<?php

namespace YonisSavary\DBWand\CLI;

use PDO;
use Throwable;
use YonisSavary\DBWand\Context;

class ConnectionFinder
{
    public function __construct(
        protected Context $context
    ) {}


    public function getConnectionFromUrl(string $url): PDO|Throwable
    {
        try {
            $parts = $this->getDSNFromURL($url);
            $dsn = $parts[0];
            $user = $parts[1];
            $password = $parts[2];

            $pdo = new PDO($dsn, $user, $password);
            return $pdo;
        } catch (Throwable $err) {
            return $err;
        }
    }


    protected function fromJsonConfiguration(): ?PDO
    {
        $output = $this->context->output;
        $config = $this->context->configuration->getJsonConfiguration();

        foreach (($config['connections'] ?? []) as $key => $value) {
            $pdoOrException = $this->getConnectionFromUrl($value);
            if ($pdoOrException instanceof PDO) {
                $output->notice("Connected from config file (connection $key)");
                return $pdoOrException;
            }
            $output->error("Could not connect through $key : " . $pdoOrException->getMessage());
        }
        return null;
    }

    /**
     * @return array as `[dsn, user, password]`
     */
    protected function getDSNFromURL(string $url): array
    {
        $url = parse_url($url);
        $user = $url['user'];
        $password = $url['pass'];

        return [
            $url['scheme'] . ':' .
                'host=' . $url['host'] . ';' .
                'port=' . $url['port'] . ';' .
                'dbname=' . str_replace('/', '', $url['path']) . ';',
            $user,
            $password
        ];
    }

    protected function promptForDBConnection(): PDO
    {
        $user = null;
        $password = null;
        do {
            $dsn = readline("DB DSN > ");
            if (str_contains($dsn, '://')) {
                $parts = $this->getDSNFromURL($dsn);
                $dsn = $parts[0];
                $user = $parts[1];
                $password = $parts[2];
            } else {
                $user = readline('DB USER > ');
                $password = readline('DB PASSWORD > ');
            }
            try {
                $connection = new PDO($dsn, $user, $password);
            } catch (Throwable $exception) {
                $this->context->output->error($exception->getMessage());
            }
        } while (!$connection);

        $this->context->output->info("Connected !");
        return $connection;
    }

    public function getAnyConnection(): PDO
    {
        return
            $this->fromJsonConfiguration() ??
            $this->promptForDBConnection();
    }
}
