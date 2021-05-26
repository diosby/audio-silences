<?php

namespace SegmentGenerator\ChapterSegmentators\Contracts;

use SegmentGenerator\Entities\Chapter;
use SegmentGenerator\Entities\SegmentCollection;

/**
 * Segments a multiple chapter.
 */
interface MultipleChapterSegmentator
{
    /**
     * Segments the given multiple chapter and adds segments to the given
     * target collection.
     *
     * @param Chapter $chapter
     * @param SegmentCollection $target
     * @return SegmentCollection
     */
    public function segment(Chapter $chapter, SegmentCollection $target): SegmentCollection;
}
