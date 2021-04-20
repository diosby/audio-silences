<?php

namespace SegmentGenerator;

/**
 * Shows logs of the debugging.
 */
trait DebugLog
{
    /**
     * A state of the debugging.
     *
     * @var bool
     */
    protected $debug = false;

    /**
     * Sets a state of the debugging.
     *
     * @param bool $state
     * @return void
     */
    public function debugMode(bool $state = true): void
    {
        $this->debug = $state;
    }

    /**
     * Shows the given log.
     *
     * @param string $message
     * @param mixed ...$args
     * @return void
     */
    private function log(string $message, ...$args)
    {
        if ($this->debug) {
            printf($message, ...$args);
        }
    }
}
