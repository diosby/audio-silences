<?php

namespace SegmentGenerator\ChapterSegmentators;

use SegmentGenerator\ChapterSegmentators\Contracts\DescriberOfMultipleChapterSegmentator;
use SegmentGenerator\ChapterSegmentators\Contracts\SegmentTitleMaker;
use SegmentGenerator\ChapterSegmentators\Contracts\MultipleChapterSegmentator as SegmentatorInterface;
use SegmentGenerator\Entities\Chapter;
use SegmentGenerator\Entities\ChapterPart;
use SegmentGenerator\Entities\Segment;
use SegmentGenerator\Entities\SegmentCollection;

/**
 * Segments multiple chapters.
 */
class MultipleChapterSegmentator implements SegmentatorInterface
{
    /**
     * A chapter analyzer.
     *
     * @var ChapterAnalyzer
     */
    protected $chapterAnalyzer;

    /**
     * A title maker.
     *
     * @var SegmentTitleMaker
     */
    protected $titleMaker;

    /**
     * A describer.
     *
     * @var DescriberOfMultipleChapterSegmentator
     */
    protected $describer;

    /**
     * A target collection with segments.
     *
     * @var SegmentCollection
     */
    private $segments;

    /**
     * A number of part.
     *
     * @var int
     */
    private $numberOfPart;

    /**
     * The counter with a segment duration of the current iteration of
     * the segmentation.
     *
     * @var int
     */
    private $counter;

    public function __construct(
        ChapterAnalyzer $chapterAnalyzer,
        SegmentTitleMaker $titleMaker,
        DescriberOfMultipleChapterSegmentator $describer
    ) {
        $this->chapterAnalyzer = $chapterAnalyzer;
        $this->titleMaker = $titleMaker;
        $this->describer = $describer;
    }

    /**
     * Segments the given multiple chapter.
     *
     * @param Chapter $chapter
     * @return SegmentCollection
     */
    public function segment(Chapter $chapter, SegmentCollection $target): SegmentCollection
    {
        $this->segments = $target;
        $this->describer->describeMultipleChapter($chapter);
        $this->numberOfPart = 0;
        $this->counter = 0;

        // С одной стороны, если бы использовали начальный код, мы бы использовали
        // переменную как глобальную в рамках функции.
        // Почему мы должны передавать часть, а не задать ее как ту же глобальную?

        foreach ($chapter->getParts() as $chapterPartIndex => $part) {
            $this->describer->setChapterPartIndex($chapterPartIndex);
            $this->handleChapterPart($part);
        }

        return $this->segments;
    }

    /**
     * Handles the given chapter part of the multiple chapter.
     *
     * @param ChapterPart $part
     * @param int $numberOfPart
     * @param int $segmentDuration
     * @return void
     */
    protected function handleChapterPart(ChapterPart $part): void
    {
        $this->describer->describePart($part);

        if ($this->chapterAnalyzer->isLongSeparablePart($part)) {
            ++$this->numberOfPart;
            $this->addLongSegment($part);
        } elseif ($this->counter === 0) {
            ++$this->numberOfPart;
            $this->startMultipleSegment($part);
        } elseif ($this->chapterAnalyzer->isSegmentOverloaded($this->counter, $part)) {
            ++$this->numberOfPart;
            $this->addOverloadedSegment($part);
        } elseif ($part->getDuration() === 0) {
            ++$this->numberOfPart;
            $this->addLastEmptySegment($part);
        } else {
            $this->addIntermediateSegment($part);
        }
    }

    /**
     * Adds the given segment as a long segment.
     *
     * @param ChapterPart $part
     * @return void
     */
    protected function addLongSegment(ChapterPart $part): void
    {
        $this->describer->describeLongPart($part);
        $this->addSegment($part, $this->numberOfPart);
        $this->counter = 0;
    }

    /**
     * Starts a new multiple segment by the given start segment of the these
     * segments.
     *
     * @param ChapterPart $part
     * @return void
     */
    protected function startMultipleSegment(ChapterPart $part): void
    {
        $this->describer->describeStartSegment($part);
        $this->addSegment($part, $this->numberOfPart);
        $this->counter += $part->getDurationWithLeftSilence();
    }

    /**
     * Adds the given segment that overloads the current segment duration.
     * The segment duration is overloaded by the duration of the chapter part
     * and its left silence.
     *
     * @param ChapterPart $part
     * @param int $numberOfPart
     * @return void
     */
    protected function addOverloadedSegment(ChapterPart $part): void
    {
        $this->addSegment($part, $this->numberOfPart);
        $this->counter = 0;
    }

    /**
     * Add the last empty segment.
     * The last empty segment has an empty duration.
     *
     * @param ChapterPart $part
     * @return void
     */
    protected function addLastEmptySegment(ChapterPart $part): void
    {
        $this->describer->describeLastEmptySegment($part);
        $this->addSegment($part, $this->numberOfPart);
    }

    /**
     * Adds an intermediate segment of the multiple segment.
     *
     * @param ChapterPart $part
     * @param int $segmentDuration
     * @return void
     */
    protected function addIntermediateSegment(ChapterPart $part): void
    {
        $this->describer->describeIntermediateSegment($part);
        $this->counter += $part->getDurationWithLeftSilence();
    }

    /**
     * Adds the given partial segment.
     *
     * @param ChapterPart $part
     * @param int $volume
     * @return Segment
     */
    protected function addSegment(ChapterPart $part, int $volume): Segment
    {
        return $this->segments->add($part->getOffset(), $this->titleMaker->fromChapterPart($part, $volume));
    }
}
