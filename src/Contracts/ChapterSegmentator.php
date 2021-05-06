<?php

namespace SegmentGenerator\Contracts;

use SegmentGenerator\Entities\ChapterCollection;
use SegmentGenerator\Entities\SegmentCollection;

/**
 * The interface to segment chapters with parts.
 */
interface ChapterSegmentator
{
    /**
     * Segments the given chapters.
     *
     * @param ChapterCollection $chapters
     * @return SegmentCollection
     */
    public function segment(ChapterCollection $chapters): SegmentCollection;
}
