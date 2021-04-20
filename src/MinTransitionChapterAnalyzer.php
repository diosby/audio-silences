<?php

namespace SegmentGenerator;

use SegmentGenerator\Contracts\ChapterAnalyzer as ServiceInterface;
use SegmentGenerator\Contracts\Duration;

/**
 * The algorithm compares durations only with the min duration of the transition.
 */
class MinTransitionChapterAnalyzer implements ServiceInterface
{
    protected $minTransition;

    protected $deviationOfTransition;

    public function __construct(int $minTransition, int $deviationOfTransition = 250)
    {
        $this->minTransition = $minTransition;
        $this->deviationOfTransition = $deviationOfTransition;
    }

    public function isTransition(Duration $duration): bool
    {
        return $this->getMinDurationOfTransition() <= $duration->getDuration();
    }

    public function getMinDurationOfTransition(): int
    {
        return $this->minTransition - $this->deviationOfTransition;
    }

    public function isPause(Duration $duration): bool
    {
        return $duration->getDuration() < $this->minTransition;
    }
}
