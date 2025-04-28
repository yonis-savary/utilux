<?php

namespace YonisSavary\DBWand\CLI;

use Exception;
use PDO;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Throwable;
use YonisSavary\DBWand\Configuration\Configuration;
use YonisSavary\DBWand\ConnectionType;
use YonisSavary\DBWand\Context;

class Terminal
{
    const MILLISECOND = 1;
    const SECOND = self::MILLISECOND * 1000;
    const MINUTE = self::SECOND * 60;
    const HOUR = self::MINUTE * 60;

    protected LoggerInterface $output;

    /** @var array<string,Command> */
    protected array $commands = [];

    protected array $currentSelection = [];

    public function __construct(?LoggerInterface $output = null)
    {
        $this->output = $output ?? new NullLogger;
        $this->loadCommands();
    }

    public function loadCommands()
    {
        $commandsDirectory = __DIR__ . "/Commands";
        foreach (scandir($commandsDirectory) as $file) {
            $file = $commandsDirectory . '/' . $file;
            if (is_file($file))
                require_once $file;
        }

        /** @var array<class-string<Command>> $commands */
        $commands = array_filter(
            get_declared_classes(),
            fn($x) => str_starts_with($x, 'YonisSavary\DBWand\CLI\Commands')
        );
        foreach ($commands as $commandClass) {
            $command = new $commandClass();
            $name = strtolower($command->name());

            if ($existing = $this->commands[$name] ?? false)
                $this->output->warning("Warning: duplicates commands with name [$name] : " . $command::class . " is erasing " . $existing::class);

            $this->commands[$name] = $command;
        }
    }

    public function printExecutionTime(int $executionTimeNano, bool $success = true)
    {
        $executionTimeMilli = $executionTimeNano / 1000000;

        $hours   = floor($executionTimeMilli / self::HOUR);
        $minutes = floor(($executionTimeMilli % self::HOUR) / self::MINUTE);
        $seconds = floor(($executionTimeMilli % self::MINUTE) / self::SECOND);
        $milliseconds = $executionTimeMilli % self::SECOND;

        $executionString =
            ($hours ? $hours . "h" : "") .
            ($minutes ? $minutes . "m" : "") .
            $seconds . "." . str_pad("$milliseconds", 3, "0", STR_PAD_LEFT) . "s";

        $message = "Execution time: $executionString";
        $success
            ? $this->output->info($message)
            : $this->output->error($message);
    }
    public function getCommand(string $name): ?Command
    {
        return $this->commands[strtolower($name)] ?? null;
    }

    public function getCommands(): array
    {
        return $this->commands;
    }

    public function getNewContext(?PDO $connection = null): Context
    {
        $context = new Context();
        $context->terminal = &$this;
        $context->output = &$this->output;

        $configuration = new Configuration($context);
        $context->configuration = &$configuration;

        if (!$connection) {
            $connectionFinder = new ConnectionFinder($context);
            $connection = $connectionFinder->getAnyConnection();
        }
        $context->changeConnection($connection);

        return $context;
    }

    public function handleScript(array|string $script, bool $skipErrors = false, ?PDO $connection = null): Context
    {
        $context = $this->getNewContext($connection);

        if (!is_array($script))
            $script = explode("\n", $script);

        foreach ($script as $line) {
            $line = ltrim($line);
            if (!$line)
                continue;

            $this->output->info(" > $line");
            $this->handlePrompt($line, $context, $skipErrors);
        }

        return $context;
    }

    public function handlePrompt(string $userPrompt, Context &$context, bool $skipErrors = false)
    {
        $argv = explode(' ', $userPrompt);
        $name = strtolower($argv[0]);
        array_shift($argv);

        if (!$name)
            return;

        if (str_starts_with($userPrompt, "#"))
            return;

        if (!($command = $this->getCommand($name))) {
            $this->output->warning("Unrecognized command name [$name]");
            return;
        }

        $context->promptHistory[] = $userPrompt;

        $startTime = hrtime(true);

        try {
            if (! $success = $command->execute($argv, $context)) {
                $this->output->error("Command failed !");
                $exception = $command->getLastException();

                if (!$skipErrors)
                    throw $exception ?? new Exception("Unknown error while processing [$userPrompt]");

                $this->output->error(
                    $exception
                        ? $exception->getMessage()
                        : "Unknown error !"
                );
            }
        } catch (Throwable $err) {
            $success = false;
            if (!$skipErrors)
                throw $err;

            $this->output->error($err->getMessage());
        }


        $executionTime = hrtime(true) - $startTime;
        $this->output->info("");
        $this->printExecutionTime($executionTime, $success);
        $this->output->info("");
    }

    public function askPrompts()
    {
        $context = $this->getNewContext();

        set_time_limit(0);
        declare(ticks=1);
        pcntl_signal(SIGINT, fn() => $this->handlePrompt("exit", $context, true));


        $this->output->info("");

        while (true) {
            $prompt = readline('[' . count($context->currentData) . '] > ');
            $context->configuration->addToPromptHistory($prompt);
            $this->handlePrompt($prompt, $context, true);
        }
    }
}
