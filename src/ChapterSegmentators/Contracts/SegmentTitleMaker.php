<?php

namespace SegmentGenerator\ChapterSegmentators\Contracts;

use SegmentGenerator\Entities\Chapter;
use SegmentGenerator\Entities\ChapterPart;

/**
 * Makes titles for segments from chapters and their parts.
 */
interface SegmentTitleMaker
{
    /**
     * Makes a title from the given chapter.
     *
     * @param Chapter $chapter
     * @return string
     */
    public function fromChapter(Chapter $chapter): string;

    /**
     * Makes a title from the given chapter part.
     *
     * @param ChapterPart $part
     * @param int $volume
     * @return string
     */
    public function fromChapterPart(ChapterPart $part, int $volume): string;
}
