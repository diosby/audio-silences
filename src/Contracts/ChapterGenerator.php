<?php

namespace SegmentGenerator\Contracts;

use SegmentGenerator\Entities\ChapterCollection;
use SegmentGenerator\Entities\Silence;

/**
 * Makes chapters by silences.
 */
interface ChapterGenerator
{
    /**
     * Generates a chapter collection from the given silences.
     *
     * @param Silence[] $silences
     * @return ChapterCollection
     */
    public function fromSilences(iterable $silences): ChapterCollection;
}
