<?php

namespace YonisSavary\DBWand\CLI;

use InvalidArgumentException;
use Throwable;
use YonisSavary\DBWand\Context;

abstract class Command
{
    protected ?Throwable $exception = null;

    public function __construct() {}

    abstract public function name(): string;
    abstract public function execute(array $argv = [], Context &$context): bool;


    protected function setLastException(Throwable $exception)
    {
        $this->exception = $exception;
    }

    public function getLastException(): ?Throwable
    {
        return $this->exception;
    }

    public function failWithException(Throwable $exception)
    {
        $this->setLastException($exception);
        return false;
    }

    public function failWithReason(string $reason, string $class = InvalidArgumentException::class): false
    {
        return $this->failWithException(new $class($reason));
    }

    public function help(): ?string
    {
        return null;
    }

    public function displayTable(array $data, Context $context)
    {
        if (!count($data))
            return $context->output->warning("Empty data / Empty results");

        $headers = array_keys($data[0]);

        if (!count($headers))
            return $context->output->warning(count($data) . " empty rows (no column selected ?)");

        $maxColumnSizes = array_map(fn($x) => mb_strlen("$x"), $headers);
        foreach ($data as $row) {
            $colNumber = 0;
            foreach ($row as $value) {
                $maxColumnSizes[$colNumber] = max($maxColumnSizes[$colNumber], mb_strlen("$value"));
                $colNumber++;
            }
        }

        $sampleLine = "+" . join('+', array_map(fn($i) => str_repeat("-", $i + 2), $maxColumnSizes)) . "+";

        $paddedValue = function (mixed $value, int $colNumber) use (&$maxColumnSizes) {
            return str_pad("$value", $maxColumnSizes[$colNumber] + (strlen("$value") - mb_strlen("$value")), ' ');
        };

        $paddedArray = function (array $row) use ($paddedValue, $sampleLine) {
            return "| " . join(" | ", array_map(fn($value, $i) => $paddedValue($value, $i), $row, range(0, count($row) - 1))) . " |";
        };

        $context->output->info($sampleLine);
        $context->output->info($paddedArray($headers));
        $context->output->info($sampleLine);
        foreach ($data as $row)
            $context->output->info($paddedArray($row));
        $context->output->info($sampleLine);
    }
}
