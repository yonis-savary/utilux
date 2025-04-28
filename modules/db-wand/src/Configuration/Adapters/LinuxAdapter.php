<?php

namespace YonisSavary\DBWand\Configuration\Adapters;

use YonisSavary\DBWand\Configuration\ConfigurationAdapter;
use YonisSavary\DBWand\Context;

class LinuxAdapter implements ConfigurationAdapter
{
    protected ?string $configDirectory = null;
    protected array $promptHistory = [];
    protected array $jsonConfiguration = [];

    public function __construct(Context $context)
    {
        $dotConfigDirectory = $_SERVER['HOME'] . "/.config";

        if (is_dir($dotConfigDirectory))
        {
            $this->configDirectory = $dotConfigDirectory . "/utilux/dbwand";
            if (!is_dir($this->configDirectory))
                mkdir($this->configDirectory, recursive:true);

            $this->loadJsonConfiguration();
            $this->loadPromptHistory();
        }
        else
        {
            $context->output->error("Could not load configuration : " . $dotConfigDirectory . " is not a directory");
        }
    }


    public function __destruct()
    {
        $this->writeJsonConfiguration();
        $this->writePromptHistory();
    }

    protected function getHistoryFilePath(): string 
    {
        $path = $this->configDirectory . "/history";
        if (!is_file($path))
            file_put_contents($path, '');

        return $path;
    }

    protected function loadJsonConfiguration()
    {
        $jsonConfigPath = $this->configDirectory . "/dbwand.json";
        $this->jsonConfiguration = is_file($jsonConfigPath)
            ? json_decode(file_get_contents($jsonConfigPath), true, 512, JSON_THROW_ON_ERROR)
            : [];
    }

    protected function writeJsonConfiguration()
    {
        $jsonConfigPath = $this->configDirectory . "/dbwand.json";
        file_put_contents($jsonConfigPath, json_encode($this->jsonConfiguration, JSON_THROW_ON_ERROR));
    }

    protected function loadPromptHistory()
    {
        $this->promptHistory = explode("\n", file_get_contents($this->getHistoryFilePath()));
        foreach ($this->promptHistory as $prompt)
            readline_add_history($prompt);
    }

    protected function writePromptHistory()
    {
        file_put_contents($this->getHistoryFilePath(), join("\n", $this->promptHistory));

    }

    // Implementation

    public function getPromptHistory(): array
    {
        return $this->promptHistory;
    }

    public function addToPromptHistory(string $prompt): void 
    {
        readline_add_history($prompt);
        $this->promptHistory[] = $prompt;
        if (count($this->promptHistory) > 100)
            array_shift($this->promptHistory);
    }

    public function getJsonConfiguration(): array
    {
        return $this->jsonConfiguration;
    }
}
