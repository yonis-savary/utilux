<?php 

namespace YonisSavary\DBWand\CLI\Commands;

use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class Template extends Command
{
    public function name(): string
    {
        return "template";
    }

    public function help(): ?string
    {
        return 'Format given message with values of current data (ex: "format INSERT INTO (...) VALUES (:id, :label, :code)"), protect sql string by default, use --raw to ignore';
    }

    public function execute(array $argv = [], Context &$context): bool
    {
        $currentData = $context->currentData;
        if (!count($currentData))
            return $this->failWithReason('No current data');
        
        $columns = array_keys($currentData[0]);
        if (!count($columns))
            return $this->failWithReason('No data field found');

        $rawMode = in_array("--raw", $argv);
        $argv = array_diff($argv, ['--raw']);
        $template = join(" ", $argv);

        $markersToReplace = [];
        preg_match_all("/:(\w+)/", $template, $matches);
        foreach ($matches[1] as $match)
        {
            if (in_array($match, $columns) && !in_array($match, $markersToReplace))
                $markersToReplace[] = $match;
        }

        foreach ($currentData as $row)
        {
            $string = $template;
            foreach ($markersToReplace as $marker)
                $string = str_replace(
                    ":$marker", 
                    $rawMode ? $row[$marker] : str_replace("'", "''", $row[$marker]), 
                    $string
                );

            $context->output->info($string);
        }

        return true;
    }
}