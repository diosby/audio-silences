<?php

namespace SegmentGenerator;

use SegmentGenerator\Contracts\Duration;

/**
 * A chapter part is a segment of a multiple segmenet.
 */
class ChapterPart implements Duration
{
    /**
     * An offset of the part from the start of the book.
     *
     * @var Interval
     */
    protected $offset;

    /**
     * A duration of the part.
     *
     * @var int
     */
    protected $duration;

    /**
     * A silence before the part.
     *
     * @var Silence|null
     */
    protected $silenceBefore;

    /**
     * A silence after the part.
     *
     * @var Silence|null
     */
    protected $silenceAfter;

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
     * Returns a silence before the part.
     *
     * @return Silence|null
     */
    public function getSilenceBefore(): ?Silence
    {
        return $this->silenceBefore;
    }

    /**
     * Sets the given silence before the part.
     *
     * @param Silence $silence
     * @return self
     */
    public function setSilenceBefore(Silence $silence): self
    {
        $this->silenceBefore = $silence;

        return $this;
    }

    /**
     * Returns a silence after the part.
     *
     * @return Silence|null
     */
    public function getSilenceAfter(): ?Silence
    {
        return $this->silenceAfter;
    }

    /**
     * Sets the given silence after the part.
     *
     * @param Silence $silence
     * @return self
     */
    public function setSilenceAfter(Silence $silence): self
    {
        $this->silenceAfter = $silence;

        return $this;
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
     * Checks whether the part is first in the chapter.
     *
     * @return bool
     */
    public function isFirst(): bool
    {
        return $this->getParent() && $this->getParent()->isFirst($this);
    }

    /**
     * Checks whether the part is last in the chapter.
     *
     * @return bool
     */
    public function isLast(): bool
    {
        return $this->getParent() && $this->getParent()->isLast($this);
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

    /**
     * Returns a duration of the part with a duration of the silence before
     * the part of the chapter.
     *
     * @return int
     */
    public function getDurationWithLeftSilence(): int
    {
        if (!$this->isFirst()) {
            $silence = $this->getSilenceBefore();
            $silenceDuration = $silence->getDuration();
        } else {
            $silenceDuration = 0;
        }

        return $this->getDuration() + $silenceDuration;
    }
}
