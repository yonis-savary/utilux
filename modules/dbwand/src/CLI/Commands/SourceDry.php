<?php 

namespace YonisSavary\DBWand\CLI\Commands;

use Throwable;
use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class SourceDry extends Command
{
    public function name(): string
    {
        return "source-dray";
    }

    public function help(): ?string
    {
        return 'Execute a script file in a transaction and abort it (ex: "source-dry /path/to/file")';
    }

    public function execute(array $argv, Context &$context): bool
    {
        if (! $script = $argv[0] ?? false)
            return $this->failWithReason("This command needs a filename");

        if (!is_file($script))
            return $this->failWithReason("[$script] is not a file");

        try
        {
            $context->utils->startTransaction();
            $context->terminal->handleScript(file_get_contents($script));
            $context->utils->rollbackTransaction();
            return true;
        }
        catch (Throwable $err)
        {
            $context->utils->rollbackTransaction();
            return $this->failWithException($err);
        }
    }
}