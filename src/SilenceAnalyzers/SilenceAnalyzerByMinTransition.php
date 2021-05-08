<?php

namespace SegmentGenerator\SilenceAnalyzers;

use SegmentGenerator\Contracts\SilenceAnalyzer;
use SegmentGenerator\Contracts\Duration;
use SegmentGenerator\Entities\Silence;

/**
 * The algorithm compares durations only with the min duration of the transition.
 */
class SilenceAnalyzerByMinTransition implements SilenceAnalyzer
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
    public function isTransition(Silence $duration): bool
    {
        return $this->minTransition <= $duration->getDuration();
    }
}
