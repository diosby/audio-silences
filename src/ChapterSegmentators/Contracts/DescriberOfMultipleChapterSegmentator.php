<?php

namespace SegmentGenerator\ChapterSegmentators\Contracts;

use SegmentGenerator\Entities\Chapter;
use SegmentGenerator\Entities\ChapterPart;

/**
 * Describes multiple chapters of a segmentator.
 */
interface DescriberOfMultipleChapterSegmentator
{
    public function setChapterPartIndex($index): self;

    public function getChapterPartIndex();

    public function setSegmentDuration(int $duration): self;

    public function getSegmentDuration(): int;

    /**
     * Describes the given multiple chapter.
     *
     * @param Chapter $chapter
     * @return void
     */
    public function describeMultipleChapter(Chapter $chapter): self;

    public function describePart(ChapterPart $part): self;

    public function describeLongPart(ChapterPart $part): self;

    public function describeStartSegment(ChapterPart $part): self;

    public function describeOverloadedSegment(ChapterPart $part): self;

    public function describeLastEmptySegment(ChapterPart $part): self;

    public function describeIntermediateSegment(ChapterPart $part): self;
}
