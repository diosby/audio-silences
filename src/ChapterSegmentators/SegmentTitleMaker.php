<?php

namespace SegmentGenerator\ChapterSegmentators;

use SegmentGenerator\ChapterSegmentators\Contracts\SegmentTitleMaker as MakerInterface;
use SegmentGenerator\Entities\Chapter;
use SegmentGenerator\Entities\ChapterPart;

/**
 * Makes titles for segments by chapters and chapter parts.
 */
class SegmentTitleMaker implements MakerInterface
{
    public function fromChapter(Chapter $chapter): string
    {
        return $chapter->getTitle();
    }

    public function fromChapterPart(ChapterPart $part, int $volume): string
    {
        $partTitle = $part->getParent()->getTitle();

        return $partTitle ? $partTitle . ", part $volume" : "Part $volume";
    }
}
