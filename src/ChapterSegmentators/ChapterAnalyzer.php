<?php

namespace SegmentGenerator\ChapterSegmentators;

use SegmentGenerator\Entities\Chapter;
use SegmentGenerator\Entities\ChapterPart;
use SegmentGenerator\Entities\Silence;

/**
 * Analyzers a chapter.
 */
class ChapterAnalyzer
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
     * Sets the given recommended max duration of a segment.
     *
     * @param int|null $value
     * @return void
     */
    public function setMaxSegment(?int $value): void
    {
        $this->maxSegment = $value;
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
     * Sets the given min duration of a silence between segments.
     *
     * @param int|null $value
     * @return void
     */
    public function setMinSilence(?int $value): void
    {
        $this->minSilence = $value;
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
            || empty($this->maxSegment)
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
    public function doesChapterHaveSeparableParts(Chapter $chapter): bool
    {
        $silences = $chapter->getInnerSilences();
        $greatSilences = array_filter($silences, function (Silence $silence) {
            return $silence->getDuration() >= $this->minSilence;
        });

        return count($greatSilences) > 0;
    }


    /**
     * Checks whether the part overloads the given segment duration.
     *
     * @param int $segmentDuration
     * @param ChapterPart $part
     * @return bool
     */
    public function doesOverload(int $segmentDuration, ChapterPart $part): bool
    {
        return $this->maxSegment <= $segmentDuration + $part->getDurationWithLeftSilence();
    }

    /**
     * Checks whether the given part is separable.
     * The separable part is a part where its duration greater or equal to
     * the minimal silence.
     *
     * @param ChapterPart $part
     * @return bool
     */
    public function isPartSeparable(ChapterPart $part): bool
    {
        return !empty($this->minSilence)
            && ($part->getSilenceAfter() && $part->getSilenceAfter()->getDuration() >= $this->minSilence)
            && ($part->getSilenceBefore() && $part->getSilenceBefore()->getDuration() >= $this->minSilence)
        ;
    }

    /**
     * Checks whether the part is long. The long part is the part where
     * its guration greater then the maximal segment.
     *
     * @param ChapterPart $part
     * @return bool
     */
    public function isLongPart(ChapterPart $part): bool
    {
        return $this->maxSegment <= $part->getDuration();
    }

    /**
     * Checks whether the given part is a long separable part.
     *
     * @param ChapterPart $part
     * @return bool
     */
    public function isLongSeparablePart(ChapterPart $part): bool
    {
        return $this->isLongPart($part) && $this->isPartSeparable($part);
    }

    /**
     * Checks whether the given segment duration is overloaded by the given
     * part and the part is separable.
     *
     * @param ChapterPart $part
     * @param int $segmentDuration
     * @return bool
     */
    public function isSegmentOverloaded(int $segmentDuration, ChapterPart $part): bool
    {
        return $this->doesOverload($segmentDuration, $part) && $this->isPartSeparable($part);
    }
}
