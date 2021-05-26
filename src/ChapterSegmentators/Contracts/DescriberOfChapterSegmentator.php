<?php

namespace SegmentGenerator\ChapterSegmentators\Contracts;

use SegmentGenerator\Entities\Chapter;
use SegmentGenerator\Entities\ChapterCollection;
use SegmentGenerator\Entities\SegmentCollection;

/**
 * Describe chapters and generated segments of the chapters.
 */
interface DescriberOfChapterSegmentator
{
    public function describerChapters(ChapterCollection $chapters): self;

    public function describeSegments(SegmentCollection $segments): self;

    public function setChapterIndex($index): self;

    public function getChapterIndex();

    public function describerChapter(Chapter $chapter): self;

    /**
    * Describes the given full chapter.
    *
    * @param Chapter $chapter
    * @return void
    */
    public function describeFullChapter(Chapter $chapter): self;
}
