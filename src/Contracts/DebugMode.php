<?php

namespace SegmentGenerator\Contracts;

/**
 * It uses to manage a state of the debuggin.
 */
interface DebugMode
{
    /**
     * Sets a state of the debugging.
     *
     * @param bool $state
     * @return void
     */
    public function debugMode(bool $state = true): void;
}
