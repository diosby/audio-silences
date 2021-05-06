<?php

namespace SegmentGenerator\Contracts;

use SegmentGenerator\Entities\SegmentCollection;
use SegmentGenerator\Entities\Silence;

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
