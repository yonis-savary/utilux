<?php 

namespace YonisSavary\DBWand\CLI\Commands;

use Throwable;
use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class Source extends Command
{
    public function name(): string
    {
        return "source";
    }

    public function help(): ?string
    {
        return 'Execute a script file (ex: "source /path/to/file")';
    }

    public function execute(array $argv = [], Context &$context): bool
    {
        if (! $script = $argv[0] ?? false)
            return $this->failWithReason("This command needs a filename");

        if (!is_file($script))
            return $this->failWithReason("[$script] is not a file");

        $results = $context->utils->transaction(function() use ($script, &$context) {
            $context->terminal->handleScript(file_get_contents($script));
        });

        if ($results instanceof Throwable)
            return $this->failWithException($results);

        return true;
    }
}