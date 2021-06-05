<?php

namespace SegmentGenerator\Contracts;

use SegmentGenerator\Entities\Silence;

/**
 * Methods to get, segment and output segments by silences.
 */
interface SegmentGeneratorFacade
{
    /**
     * Returns an iterator of silences.
     *
     * @return Silence[]
     */
    public function getSilences(): iterable;

    /**
     * Returns a silence segmentator.
     *
     * @return SilenceSegmentator
     */
    public function getSegmentator(): SilenceSegmentator;

    /**
     * Returns an outputter.
     *
     * @return Outputter
     */
    public function getOutputter(): Outputter;
}
