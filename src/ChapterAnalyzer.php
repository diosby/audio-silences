<?php

namespace SegmentGenerator;

use SegmentGenerator\Contracts\ChapterAnalyzer as ServiceInterface;
use SegmentGenerator\Contracts\Duration;

class ChapterAnalyzer implements ServiceInterface
{
    protected $silenceOfTransition;

    protected $deviationOfTransition;

    protected $silenceOfPause;

    protected $deviationOfPause;

    public function __construct(
        int $silenceOfTransition,
        int $silenceOfPause,
        int $deviationOfTransition = 250,
        int $deviationOfPause = 100
    ) {
        $this->silenceOfTransition = $silenceOfTransition;
        $this->deviationOfTransition = $deviationOfTransition;
        $this->silenceOfPause = $silenceOfPause;
        $this->deviationOfPause = $deviationOfPause;
    }

    public function isTransition(Duration $duration): bool
    {
        return $this->getMinDurationOfTransition() <= $duration->getDuration();
    }

    public function getMinDurationOfTransition(): int
    {
        return $this->silenceOfTransition - $this->deviationOfTransition;
    }

    public function isPause(Duration $duration): bool
    {
        $time = $duration->getDuration();

        return $time <= $this->getMaxTimeOfPause();
    }

    public function getMaxTimeOfPause(): int
    {
        return $this->silenceOfPause + $this->deviationOfPause;
    }
}
