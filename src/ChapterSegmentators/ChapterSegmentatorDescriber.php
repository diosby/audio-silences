<?php

namespace SegmentGenerator\ChapterSegmentators;

use SegmentGenerator\ChapterSegmentators\Contracts\{
    DescriberOfChapterSegmentator as ChapterDescriber,
    DescriberOfMultipleChapterSegmentator as ChapterPartDescriber
};
use SegmentGenerator\Contracts\Logger;
use SegmentGenerator\Entities\Chapter;
use SegmentGenerator\Entities\ChapterCollection;
use SegmentGenerator\Entities\ChapterPart;
use SegmentGenerator\Entities\SegmentCollection;

/**
 * Describes chapters and chapter parts.
 */
class ChapterSegmentatorDescriber implements
    ChapterDescriber,
    ChapterPartDescriber
{
    /**
     * An index of the current chapter.
     *
     * @var int|string
     */
    private $chapterIndex = 0;

    /**
     * An index of the current chapter part.
     *
     * @var int|string
     */
    private $chapterPartIndex = 0;

    /**
     * A value of the counter of the segment duration.
     *
     * @var int
     */
    private $segmentDuration;

    /**
     * A logger.
     *
     * @var Logger
     */
    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function describerChapters(ChapterCollection $chapters): ChapterDescriber
    {
        return $this;
    }

    public function describeSegments(SegmentCollection $segments): ChapterDescriber
    {
        return $this;
    }

    public function setChapterIndex($index): ChapterDescriber
    {
        $this->chapterIndex = $index;

        return $this;
    }

    public function getChapterIndex()
    {
        return $this->chapterIndex;
    }


    public function setChapterPartIndex($index): ChapterPartDescriber
    {
        $this->chapterPartIndex = $index;

        return $this;
    }

    public function getChapterPartIndex()
    {
        return $this->chapterPartIndex;
    }

    public function setSegmentDuration(int $duration): ChapterPartDescriber
    {

        return $this;
    }

    public function getSegmentDuration(): int
    {
        return $this->segmentDuration;
    }

    public function describerChapter(Chapter $chapter): ChapterDescriber
    {

        return $this;
    }

    /**
    * Describes the given full chapter.
    *
    * @param Chapter $chapter
    * @return void
    */
   public function describeFullChapter(Chapter $chapter): ChapterDescriber
   {
       $this->logger->log(
           "#%s is a full chapter. The duration: %d ms.\n",
           $this->chapterIndex,
           $chapter->getDuration()
       );

       return $this;
   }

    /**
     * Describes the given multiple chapter.
     *
     * @param Chapter $chapter
     * @return ChapterPartDescriber
     */
    public function describeMultipleChapter(Chapter $chapter): ChapterPartDescriber
    {
        $this->logger->log(
            "#%s is a multiple chapter. The duration: %d ms; the parts: %s.\n",
            $this->chapterIndex,
            $chapter->getDuration(),
            $chapter->count()
        );

        return $this;
    }

    public function describePart(ChapterPart $part): ChapterPartDescriber
    {
        $this->logger->log(
            "#%s.%s. The duration of the part is %d ms.\n",
            $this->chapterIndex,
            $this->chapterPartIndex,
            $part->getDuration()
        );

        return $this;
    }

    public function describeLongPart(ChapterPart $part): ChapterPartDescriber
    {
        $this->logger->log("[L] The part is greater than the max segment.\n");

        return $this;
    }

    public function describeStartSegment(ChapterPart $part): ChapterPartDescriber
    {
        $this->logger->log("[F] A new segment of multiple segments.\n");

        return $this;
    }

    public function describeOverloadedSegment(ChapterPart $part): ChapterPartDescriber
    {
        $this->logger->log(
            "[O] The duration of the part with its left silence is overloaded: %d.\n",
            $this->segmentDuration + $part->getDurationWithLeftSilence()
        );

        return $this;
    }

    public function describeLastEmptySegment(ChapterPart $part): ChapterPartDescriber
    {
        $this->logger->log("[E] The last part of the chapter. It is empty.\n");

        return $this;
    }

    public function describeIntermediateSegment(ChapterPart $part): ChapterPartDescriber
    {
        $this->logger->log("[N] To the next segment. Add the duration.\n");

        return $this;
    }
}
