<?php

namespace YonisSavary\DBWand\Configuration\Adapters;

use YonisSavary\DBWand\Configuration\ConfigurationAdapter;
use YonisSavary\DBWand\Context;

class UnsupportedOSAdapter implements ConfigurationAdapter
{
    public function __construct(Context $context) {}

    public function getPromptHistory(): array
    {
        return [];
    }

    public function addToPromptHistory(string $prompt): void {}

    public function getJsonConfiguration(): array
    {
        return [];
    }
}
