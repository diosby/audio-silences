<?php

namespace SegmentGenerator\Contracts;

interface Duration
{
    /**
     * Returns a duration of the interval.
     *
     * @return int
     */
    public function getDuration(): int;
}
