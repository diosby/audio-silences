<?php

namespace SegmentGenerator\Loggers;

use SegmentGenerator\Contracts\Logger;

/**
 * Ignores logs.
 */
class NullLogger implements Logger
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
    }
}
