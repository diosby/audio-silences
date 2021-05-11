<?php

namespace SegmentGenerator\SilenceSegmentators;

use SegmentGenerator\Contracts\Logger;
use SegmentGenerator\Contracts\SilenceSegmentator;
use SegmentGenerator\Entities\SegmentCollection;
use SegmentGenerator\Entities\Silence;
use SegmentGenerator\SilenceSegmentators\Contracts\SilenceSegmentatorWithChapters;

/**
 * Segments silences and logs it.
 */
class SilenceSegmentatorWithLogger implements SilenceSegmentator
{
    /**
     * A silence segmentator.
     *
     * @var SilenceSegmentatorWithChapters
     */
    protected $segmentator;

    /**
     * A logger.
     *
     * @var Logger
     */
    protected $logger;

    public function __construct(SilenceSegmentatorWithChapters $segmentator, Logger $logger)
    {
        $this->segmentator = $segmentator;
        $this->logger = $logger;
    }

    /**
     * Segments the given silences.
     *
     * @param Silence[] $silences
     * @return SegmentCollection
     */
    public function segment(array $silences): SegmentCollection
    {
        $segments = $this->segmentator->segment($silences);
        $chapters = $this->segmentator->getChapters();

        $this->logger->log("A number of the chapters: %d.\n", $chapters->getNumberOfChapters());
        $this->logger->log("A number of the parts of the chapters: %d.\n", $chapters->getNumberOfParts());
        $this->logger->log("A duration of the chapters without silences between chapters: %dms.\n", $chapters->getDuration());

        return $segments;
    }
}
