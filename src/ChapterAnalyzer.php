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

    protected $deviationOfTransition;

    public function __construct(int $minTransition, int $deviationOfTransition = 250)
    {
        $this->minTransition = $minTransition;
        $this->deviationOfTransition = $deviationOfTransition;
    }

    /**
     * Checks whether the duration matches the transition.
     *
     * @param Duration $duration
     * @return bool
     */
    public function isTransition(Duration $duration): bool
    {
        return $this->getMinDurationOfTransition() <= $duration->getDuration();
    }

    /**
     * Returns a min duration of the transition with the deviation.
     *
     * @return int
     */
    public function getMinDurationOfTransition(): int
    {
        return $this->minTransition - $this->deviationOfTransition;
    }
}
