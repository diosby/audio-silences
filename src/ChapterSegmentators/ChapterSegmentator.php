<?php

namespace SegmentGenerator\ChapterSegmentators;

use SegmentGenerator\Contracts\ChapterSegmentator as SegmentatorInterface;
use SegmentGenerator\Entities\Chapter;
use SegmentGenerator\Entities\ChapterCollection;
use SegmentGenerator\Entities\ChapterPart;
use SegmentGenerator\Entities\Segment;
use SegmentGenerator\Entities\SegmentCollection;

/**
 * Segments chapters.
 */
class ChapterSegmentator implements SegmentatorInterface
{
    /**
     * A chapter analyzer.
     *
     * @var ChapterAnalyzer
     */
    protected $chapterAnalyzer;
    /**
     * A recommended maximal duration of a segment.
     *
     * @var int|null
     */
    protected $maxSegment;

    /**
     * A minimal duration of a silence between parts (segments) in a chapter
     * which can be used to split a long chapter.
     *
     * @var int|null
     */
    protected $minSilence;

    private $segments = [];


    /**
     * An index of the current handled chapter part.
     *
     * @var int|string
     */
    protected $chapterPartIndex;

    {
        $this->chapterAnalyzer = new ChapterAnalyzer($maxSegment, $minSilence);
    }

    /**
     * A recommended max duration of a segment.
     *
     * @return int|null
     */
    public function getMaxDurationOfSegment(): ?int
    {
        return $this->chapterAnalyzer->getMaxDurationOfSegment();
    }

    /**
     * Returns a min duration of a silence between segments.
     *
     * @return int|null
     */
    public function getMinSilence(): ?int
    {
        return $this->chapterAnalyzer->getMinSilence();
    }

    /**
     * Segments the given chapters.
     *
     * @param ChapterCollection $chapters
     * @return SegmentCollection
     */
    public function segment(ChapterCollection $chapters): SegmentCollection
    {
        $this->segments = [];

        foreach ($chapters->getItems() as $chapter) {
            if ($this->chapterAnalyzer->isUnbreakable($chapter)) {
                $this->pushFullChapter($chapter);
            } else {
                $this->pushMultipleChapter($chapter);
            }
        }

        return new SegmentCollection($this->segments);
    }

    /**
     * Pushes the given full chapter.
     *
     * @param Chapter $chapter
     * @return void
     */
    public function pushFullChapter(Chapter $chapter): void
    {
        $this->segments[] = new Segment($chapter->getOffset(), $chapter->getTitle());
    }

    /**
     * Pushes the given multiple chapter.
     *
     * @param Chapter $chapter
     * @return void
     */
    public function pushMultipleChapter(Chapter $chapter): void
    {
        $numberOfPart = 0;
        $segmentDuration = 0;

        foreach ($chapter->getParts() as $this->chapterPartIndex => $part) {
            $this->handlePart($part, $numberOfPart, $segmentDuration);
        }
    }

    /**
     * Handles the given chapter part of the multiple chapter.
     *
     * @param ChapterPart $part
     * @param int $numberOfPart
     * @param int $segmentDuration
     * @return void
     */
    protected function handlePart(ChapterPart $part, int &$numberOfPart, int &$segmentDuration): void
    {
        if ($this->chapterAnalyzer->isLongSeparablePart($part)) {
            $this->addLongSegment($part, ++$numberOfPart, $segmentDuration);
        } elseif ($segmentDuration === 0) {
            $this->startMultipleSegment($part, ++$numberOfPart, $segmentDuration);
        } elseif ($this->chapterAnalyzer->isSegmentOverloaded($segmentDuration, $part)) {
            $this->addOverloadedSegment($part, ++$numberOfPart, $segmentDuration);
        } elseif ($part->getDuration() === 0) {
            $this->addLastEmptySegment($part, ++$numberOfPart);
        } else {
            $this->addIntermediateSegment($part, $segmentDuration);
        }
    }
    }

    /**
     * Adds the given segment as a long segment.
     *
     * @param ChapterPart $part
     * @param int $numberOfPart
     * @param int $segmentDuration
     * @return void
     */
    protected function addLongSegment(ChapterPart $part, int $numberOfPart, int &$segmentDuration): void
    {
        $this->addSegment($part, $numberOfPart);
        $segmentDuration = 0;
    }

    /**
     * Starts a new multiple segment by the given start segment of the these
     * segments.
     *
     * @param ChapterPart $part
     * @param int $numberOfPart
     * @param int $segmentDuration
     * @return void
     */
    protected function startMultipleSegment(ChapterPart $part, int $numberOfPart, int &$segmentDuration): void
    {
        $this->addSegment($part, $numberOfPart);
        $segmentDuration += $part->getDurationWithLeftSilence();
    }

    /**
     * Adds the given segment that overloads the current segment duration.
     * The segment duration is overloaded by the duration of the chapter part
     * and its left silence.
     *
     * @param ChapterPart $part
     * @param int $numberOfPart
     * @param int $segmentDuration
     * @return void
     */
    protected function addOverloadedSegment(ChapterPart $part, int $numberOfPart, int &$segmentDuration): void
    {
        $this->addSegment($part, $numberOfPart);
        $segmentDuration = 0;
    }

    /**
     * Add the last empty segment.
     * The last empty segment has an empty duration.
     *
     * @param ChapterPart $part
     * @param int $numberOfPart
     * @return void
     */
    protected function addLastEmptySegment(ChapterPart $part, int $numberOfPart): void
    {
        $this->addSegment($part, ++$numberOfPart);
    }

    /**
     * Adds an intermediate segment of the multiple segment.
     *
     * @param ChapterPart $part
     * @param int $segmentDuration
     * @return void
     */
    protected function addIntermediateSegment(ChapterPart $part, int &$segmentDuration): void
    {
        $segmentDuration += $part->getDurationWithLeftSilence();
    }

    /**
     * Adds a new segment by the given chapter part and returns it.
     *
     * @param ChapterPart $part
     * @param int $volume
     * @return Segment
     */
    public function addSegment(ChapterPart $part, int $volume): Segment
    {
        $this->segments[] = $lastSegment = new Segment($part->getOffset(), $part->getParent()->getTitle());
        $title = $lastSegment->getTitle() ? $lastSegment->getTitle() . ", part $volume" : "Part $volume";
        $lastSegment->setTitle($title);

        return $lastSegment;
    }
}
