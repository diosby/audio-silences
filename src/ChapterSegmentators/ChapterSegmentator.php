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

    public function __construct(int $maxSegment = null, int $minSilence = null)
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

        foreach ($chapter->getParts() as $key => $part) {
            if ($this->chapterAnalyzer->isLongSeparablePart($part)) {
                // It is a big segment.
                $this->addSegment($part, ++$numberOfPart);
                $segmentDuration = 0;
            } elseif ($segmentDuration === 0) {
                // It is a start segment of the multiple segments.
                $this->addSegment($part, ++$numberOfPart);
                $segmentDuration += $part->getDurationWithLeftSilence();
            } elseif ($this->chapterAnalyzer->isSegmentOverloaded($segmentDuration, $part)) {
                // The segment duration is overloaded by the duration of the chapter part and its left silence.
                $this->addSegment($part, ++$numberOfPart);
                $segmentDuration = 0;
            } elseif ($part->getDuration() === 0) {
                // The last empty segment that has an empty duration.
                $this->addSegment($part, ++$numberOfPart);
            } else {
                $segmentDuration += $part->getDurationWithLeftSilence();
            }
        }
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
