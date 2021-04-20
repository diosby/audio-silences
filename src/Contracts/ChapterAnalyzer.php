<?php

namespace SegmentGenerator\Contracts;

/**
 * The interface to analyze durationns of silences and detects transitions and
 * pauses of chapters.
 */
interface ChapterAnalyzer
{
    /**
     * Checks whether the duration matches the transition.
     *
     * @param Duration $duration
     * @return bool
     */
    public function isTransition(Duration $duration): bool;

    /**
     * Checks whether the duration matches the pause.
     *
     * @param Duration $duration
     * @return bool
     */
    public function isPause(Duration $duration): bool;
}
