<?php

namespace SegmentGenerator;

use SegmentGenerator\Contracts\ChapterAnalyzer as ServiceInterface;
use SegmentGenerator\Contracts\Duration;

/**
 * The algorithm compares durations only with the min duration of the transition.
 */
class ChapterAnalyzer implements ServiceInterface
{
    protected $minTransition;

    public function __construct(int $minTransition)
    {
        $this->minTransition = $minTransition;
    }

    /**
     * Checks whether the duration matches the transition.
     *
     * @param Duration $duration
     * @return bool
     */
    public function isTransition(Duration $duration): bool
    {
        return $this->minTransition <= $duration->getDuration();
    }
}
