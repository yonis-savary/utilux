<?php 

namespace YonisSavary\DBWand\Tests\CLI;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Stringable;

class ArrayLogger extends AbstractLogger
{
    /** @var ArrayLoggerLine[] $lines */
    protected array $lines = [];

    /**
     * @return ArrayLoggerLine[]
     */
    public function lines(): array 
    {
        return $this->lines;
    }

    /**
     * Interpolates context values into the message placeholders.
     */
    function interpolate($message, array $context = array())
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            // check that the value can be cast to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

    public function log($level, string|Stringable $message, array $context = []): void
    {
        $this->lines[] = new ArrayLoggerLine($level, $message, $context, $this->interpolate($message, $context));
    }
}