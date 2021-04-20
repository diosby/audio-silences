<?php

namespace SegmentGenerator\Contracts;

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
