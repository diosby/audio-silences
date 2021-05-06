<?php

namespace SegmentGenerator\Entities;

use SegmentGenerator\Contracts\Duration;

/**
 * The class of an interval.
 * It was implemented intead of DateInterval, because DateInterval doesn't
 * support milliseconds as ISO 8601.
 *
 * @link https://en.wikipedia.org/wiki/ISO_8601#Durations
 */
class Interval extends \DateInterval implements Duration
{
    protected $ms = 0;

    /**
     * Initializen an instance of the interval.
     *
     * @param string $interval PnYnMnDTnHnMnS
     */
    public function __construct(string $interval)
    {
        // Replaces a nonstandard interval to PHP format and pulls milliseconds.
        if ($position = strpos($interval, '.')) {
            $this->ms = (int) substr($interval, $position + 1, (strlen($interval) - 2 - $position));
            $interval = substr($interval, 0, $position) . 'S';
        }

        parent::__construct($interval);
    }

    public function difference(self $interval): float
    {
        return $this->getDuration() - $interval->getDuration();
    }

    public function getDays(): int
    {
        return $this->d;
    }

    public function getHours(): int
    {
        return $this->h;
    }

    public function getMinutes(): int
    {
        return $this->i;
    }

    public function getSeconds(): int
    {
        return $this->s;
    }

    public function getMilliseconds(): int
    {
        return $this->ms;
    }

    public function getDuration(): int
    {
        $days = $this->d * 24 * 60 * 60 * 1000;
        $hours = $this->h * 60 * 60 * 1000;
        $minutes = $this->i * 60 * 1000;
        $seconds = $this->s * 1000;

        return $this->ms + $seconds + $minutes + $hours + $days;
    }

    public function __toString(): string
    {
        $interval = 'P';
        $date = $time = '';

        if (!empty($this->y)) {
            $date .= $this->y . 'Y';
        }

        if (!empty($this->m)) {
            $date .= $this->m . 'M';
        }

        if (!empty($this->d)) {
            $date .= $this->d . 'D';
        }

        if (!empty($this->h)) {
            $time .= $this->h . 'H';
        }

        if (!empty($this->i)) {
            $time .= $this->i . 'M';
        }

        if (!empty($this->s)) {
            $time .= $this->s;

            if (!empty($this->ms)) {
                $time .= '.' . $this->ms;
            }

            $time .= 'S';
        }

        $interval .= $date;

        if (strlen($time)) {
            $interval .= 'T' . $time;
        } else {
            $interval .= 'T0S';
        }

        return $interval;
    }
}
