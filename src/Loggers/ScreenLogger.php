<?php

namespace SegmentGenerator\Loggers;

use SegmentGenerator\Contracts\Logger;

/**
 * Outputs logs to the screen.
 */
class ScreenLogger implements Logger
{
    /**
     * Logs the given message and args.
     *
     * @param string $message
     * @param mixed ...$args
     * @return void
     */
    public function log(string $message, ...$args): void
    {
        printf($message, ...$args);
    }
}
