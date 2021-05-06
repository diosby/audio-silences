<?php

namespace SegmentGenerator\Contracts;

use SegmentGenerator\SegmentCollection;
use SegmentGenerator\Silence;

/**
 * Segments silences.
 */
interface SilenceSegmentator
{
    /**
     * Segments the given silences.
     *
     * @param Silence[] $silences
     * @return SegmentCollection
     */
    public function segment(array $silences): SegmentCollection;
}
