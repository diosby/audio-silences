<?php

namespace SegmentGenerator\Entities;

use SegmentGenerator\Contracts\Duration;

/**
 * Keeps intervals of a silence.
 */
class Silence implements Duration
{
    protected $from;
    protected $until;

    public function __construct(Interval $from, Interval $until)
    {
        $this->from = $from;
        $this->until = $until;
    }

    /**
     * Returns a 'from' interval of the silence.
     *
     * @return Interval
     */
    public function getFrom(): Interval
    {
        return $this->from;
    }

    /**
     * Returns an 'until' interval of the silence.
     *
     * @return Interval
     */
    public function getUntil(): Interval
    {
        return $this->until;
    }

    /**
     * Returns a duration of the silence.
     *
     * @return int
     */
    public function getDuration(): int
    {
        return $this->until->difference($this->from);
    }
}
