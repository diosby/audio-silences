<?php

namespace SegmentGenerator\ChapterSegmenrators;

use SegmentGenerator\Contracts\ChapterSegmentator as SegmentatorInterface;
use SegmentGenerator\Entities\Chapter;
use SegmentGenerator\Entities\ChapterCollection;
use SegmentGenerator\Entities\ChapterPart;
use SegmentGenerator\Entities\Segment;
use SegmentGenerator\Entities\SegmentCollection;
use SegmentGenerator\Entities\Silence;

/**
 * Segments chapters.
 */
class ChapterSegmentator implements SegmentatorInterface
{
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
        $this->maxSegment = $maxSegment;
        $this->minSilence = $minSilence;
    }

    /**
     * A recommended max duration of a segment.
     *
     * @return int|null
     */
    public function getMaxDurationOfSegment(): ?int
    {
        return $this->maxSegment;
    }

    /**
     * Returns a min duration of a silence between segments.
     *
     * @return int|null
     */
    public function getMinSilence(): ?int
    {
        return $this->minSilence;
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

        foreach ($chapters->getItems() as $key => $chapter) {
            if (empty($this->maxSegment) || $this->isUnbreakable($chapter)) {
                $this->fullChapter($chapter, $key + 1);
            } else {
                $this->multipleChapter($chapter, $key + 1);
            }
        }

        return new SegmentCollection($this->segments);
    }

    /**
     * Checks whethet the given chapter is unbreakable.
     * The unbreakable chapter is that that has one part or the max duration of
     * a segment greater then the chapter or the min silence isn't in
     * the chapter.
     *
     * @param Chapter $chapter
     * @return bool
     */
    public function isUnbreakable(Chapter $chapter): bool
    {
        return $chapter->count() === 1
            || (isset($this->maxSegment) && $this->maxSegment >= $chapter->getDuration())
            || (isset($this->minSilence) && !$this->doesChapterHaveSeparableParts($chapter))
        ;
    }

    /**
     * Checks whether the given chapter has any separable parts.
     * The separable part is a part where a silence duration greater than the
     * min silence.
     *
     * @param Chapter $chapter
     * @return bool
     */
    protected function doesChapterHaveSeparableParts(Chapter $chapter): bool
    {
        $silences = $chapter->getInnerSilences();
        $greatSilences = array_filter($silences, function (Silence $silence) {
            return $silence->getDuration() >= $this->minSilence;
        });

        return count($greatSilences) > 0;
    }

    /**
     * Adds the given full chapter.
     *
     * @param Chapter $chapter
     * @param int $index
     * @return void
     */
    protected function fullChapter(Chapter $chapter, int $index): void
    {
        $this->segments[] = new Segment($chapter->getOffset(), $chapter->getTitle());
    }

    /**
     * Adds the given multiple chapter.
     *
     * @param Chapter $chapter
     * @param int $index
     * @return void
     */
    protected function multipleChapter(Chapter $chapter, int $index): void
    {
        $numberOfPart = 0;
        $segmentDuration = 0;

        foreach ($chapter->getParts() as $key => $part) {
            if ($this->maxSegment <= $part->getDuration() && $this->isPartSeparable($part)) {
                // It is a big segment.
                $this->partialSegment($part, ++$numberOfPart);
                $segmentDuration = 0;
            } elseif ($segmentDuration === 0) {
                // It is a start segment of the multiple segments.
                $this->partialSegment($part, ++$numberOfPart);
                $segmentDuration += $part->getDurationWithLeftSilence();
            } elseif ($this->isOverload($segmentDuration, $part) && $this->isPartSeparable($part)) {
                // The segment duration is overloaded by the duration of the chapter part and its left silence.
                $this->partialSegment($part, ++$numberOfPart);
                $segmentDuration = 0;
            } elseif ($part->getDuration() === 0) {
                // The last empty segment that has an empty duration.
                $this->partialSegment($part, ++$numberOfPart);
            } else {
                $segmentDuration += $part->getDurationWithLeftSilence();
            }
        }
    }

    /**
     * Adds the given partial segment.
     *
     * @param ChapterPart $part
     * @param int $index
     * @return void
     */
    protected function partialSegment(ChapterPart $part, int $index): void
    {
        $this->segments[] = $lastSegment = new Segment($part->getOffset(), $part->getParent()->getTitle());
        $title = $lastSegment->getTitle() ? $lastSegment->getTitle() . ", part $index" : "Part $index";
        $lastSegment->setTitle($title);
    }

    /**
     * Checks whether the part overloads the given segment duration.
     *
     * @param int $segmentDuration
     * @param ChapterPart $part
     * @return bool
     */
    protected function isOverload(int $segmentDuration, ChapterPart $part): bool
    {
        return $this->maxSegment <= $segmentDuration + $part->getDurationWithLeftSilence();
    }

    /**
     * Checks whether the given part is separable.
     * The separable part is a part that has a duration greater or equal to
     * the min silence.
     *
     * @param ChapterPart $part
     * @return bool
     */
    protected function isPartSeparable(ChapterPart $part): bool
    {
        return !empty($this->minSilence)
            && ($part->getSilenceAfter() && $part->getSilenceAfter()->getDuration() >= $this->minSilence)
            && ($part->getSilenceBefore() && $part->getSilenceBefore()->getDuration() >= $this->minSilence)
        ;
    }
}
