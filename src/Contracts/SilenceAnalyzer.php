<?php

namespace SegmentGenerator\Contracts;

use SegmentGenerator\Entities\Silence;

/**
 * Analyzes a silence and detects a transition of chapters.
 */
interface SilenceAnalyzer
{
    /**
     * Checks whether the silence matches the transition for a chapter.
     *
     * @param Silence $silence
     * @return bool
     */
    public function isTransition(Silence $duration): bool;
}
