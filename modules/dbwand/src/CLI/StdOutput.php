<?php

namespace YonisSavary\DBWand\CLI;

use Psr\Log\AbstractLogger;

class StdOutput extends AbstractLogger
{
    const LEVEL_FORMAT = [
        'emergency' => "\033[41;37m",
        'alert'     => "\033[31;1m",
        'critical'  => "\033[31m",
        'error'     => "\033[31m",
        'warning'   => "\033[33m",
        'notice'    => "\033[34m",
        'info'      => "",
        'debug'     => "\033[35m",
    ];

    function interpolate($message, array $context = array())
    {
        $replace = array();
        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        return strtr($message, $replace);
    }




    public function log($level, string|\Stringable $message, array $context = []): void
    {
        echo (self::LEVEL_FORMAT[$level] ?? "") .  $this->interpolate($message, $context) . "\n\033[0m";
    }
}
