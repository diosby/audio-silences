<?php

namespace SegmentGenerator\Contracts;

use SegmentGenerator\ChapterCollection;
use SegmentGenerator\SegmentCollection;

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
