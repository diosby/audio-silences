<?php

namespace SegmentGenerator;

use SegmentGenerator\Contracts\Duration;

/**
 * A chapter part is a segment of a multiple segmenet.
 */
class ChapterPart implements Duration
{
    protected $offset;

    protected $duration;

    /**
     * A parent chapter.
     *
     * @var Chapter|null
     */
    protected $parent;

    public function __construct(Interval $offset, int $duration = 0, Chapter $parent = null)
    {
        $this->offset = $offset;
        $this->duration = $duration;
        $this->parent = $parent;
    }

    /**
     * Returns a parent chapter.
     *
     * @return Chapter|null
     */
    public function getParent(): ?Chapter
    {
        return $this->parent;
    }

    /**
     * Sets the given duration to the part.
     *
     * @param int $duration
     * @return void
     */
    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * Sets a duration by a difference of the given finish interval.
     *
     * @param Interval $finish
     * @return void
     */
    public function setDurationByInterval(Interval $finish): void
    {
        $this->duration = $finish->difference($this->offset);
    }

    /**
     * Returns an offset of the part.
     *
     * @return Interval
     */
    public function getOffset(): Interval
    {
        return $this->offset;
    }

    /**
     * Returns a duration of the part.
     *
     * @return int
     */
    public function getDuration(): int
    {
        return $this->duration;
    }
}
