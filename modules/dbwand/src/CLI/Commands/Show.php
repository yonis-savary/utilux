<?php 

namespace YonisSavary\DBWand\CLI\Commands;

use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;

class Show extends Command
{
    public function name(): string
    {
        return "show";
    }

    public function help(): ?string
    {
        return 'Show some row in the current data set (ex: "show 5", "show 0-99", "show 2-10 --json")';
    }

    public function execute(array $argv, Context &$context): bool
    {
        $jsonMode = in_array("--json", $argv);
        $argv = array_diff($argv, ["--json"]);

        $index = $argv[0] ?? "0-10";

        $dataset = $context->dataset;

        $dataToShow = [];
        if (is_numeric($index))
        {
            $context->output->notice("Showing row number $index");
            $dataToShow = [$dataset[$index]] ?? ['no result' => "No result found at row $index"];
        }
        else if (preg_match("/^\d+-\d+$/", $index))
        {
            list($start, $end) = explode("-", $index);
            $context->output->notice("Showing rows from $start to $end");
            $dataToShow = array_values(array_slice($dataset, $start, $end-$start, true));
        }
        else if ($index == "all")
        {
            $dataToShow = &$dataset;
        }
        else
        {
            $context->output->notice("Unrecognized index format [$index]");
            $context->output->notice("Showing first 10 rows");
            $dataToShow = array_values(array_slice($dataset, 0, 10, true));
        }

        $context->output->info("");
        $context->output->info("Data :");

        if ($jsonMode)
        {
            $json = json_encode($dataToShow, JSON_PRETTY_PRINT);
            $context->output->info($json);
        }
        else 
        {
            $this->displayTable($dataToShow, $context);
        }
        return true;
    }
}