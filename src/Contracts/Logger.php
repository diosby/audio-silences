<?php

namespace SegmentGenerator\Contracts;

interface Logger
{
    /**
     * Logs the given message and args.
     *
     * @param string $message
     * @param mixed ...$args
     * @return void
     */
    public function log(string $message, ...$args): void;
}
