<?php

namespace SegmentGenerator\Contracts;

use SegmentGenerator\Entities\SegmentCollection;

/**
 * Outputs a collection with segments.
 */
interface Outputter
{
    /**
     * Outputs the given segments.
     *
     * @param SegmentCollection $segments
     * @return void
     */
    public function output(SegmentCollection $segments): void;
}
