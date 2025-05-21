<?php

namespace YonisSavary\DBWand\Configuration;

use YonisSavary\DBWand\Configuration\Adapters\LinuxAdapter;
use YonisSavary\DBWand\Configuration\Adapters\UnsupportedOSAdapter;
use YonisSavary\DBWand\Context;

class Configuration implements ConfigurationAdapter
{
    protected static ?self $instance = null;
    protected ConfigurationAdapter $adapter;

    public function __construct(Context &$context)
    {
        if (self::$instance)
            return self::$instance;

        self::$instance = &$this;

        $os = strtolower(PHP_OS);

        if (str_contains($os, 'linux')) {
            $this->adapter = new LinuxAdapter($context);
        } else {
            $context->output->warning("Unsupported configuration for os [$os]");
            $this->adapter = new UnsupportedOSAdapter($context);
        }
    }

    public function getPromptHistory(): array
    {
        return $this->adapter->getPromptHistory();
    }

    public function addToPromptHistory(string $prompt): void
    {
        $this->adapter->addToPromptHistory($prompt);
    }

    public function getJsonConfiguration(): array
    {
        return $this->adapter->getJsonConfiguration();
    }
}
