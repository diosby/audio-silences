<?php

namespace SegmentGenerator\SilenceSegmentators\Contracts;

use SegmentGenerator\Contracts\SilenceSegmentator;
use SegmentGenerator\Entities\ChapterCollection;

/**
 * Segments silences and keeps chapters of the last segmentation.
 */
interface SilenceSegmentatorWithChapters extends SilenceSegmentator
{
    /**
     * Returns the last generated chapters.
     *
     * @return ChapterCollection|null
     */
    public function getChapters(): ?ChapterCollection;
}
