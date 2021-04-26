<?php

namespace SegmentGenerator\Contracts;

use SegmentGenerator\ChapterCollection;
use SegmentGenerator\Contracts\DebugMode;

/**
 * Makes chapters by silences.
 */
interface ChapterGenerator extends DebugMode
{
    /**
     * Generates a chapter collection from the given silences.
     *
     * @param Silence[] $silences
     * @return ChapterCollection
     */
    public function fromSilences(iterable $silences): ChapterCollection;
}
