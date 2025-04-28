<?php 

namespace YonisSavary\DBWand\Configuration;

use YonisSavary\DBWand\Context;

interface ConfigurationAdapter
{
    public function __construct(Context $context);

    public function getPromptHistory(): array;
    public function addToPromptHistory(string $prompt): void;

    public function getJsonConfiguration(): array;
}