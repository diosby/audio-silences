<?php

namespace SegmentGenerator\SilenceSegmentators;

use SegmentGenerator\Contracts\ChapterGenerator;
use SegmentGenerator\Contracts\ChapterSegmentator;
use SegmentGenerator\Contracts\Logger;
use SegmentGenerator\Contracts\SilenceSegmentator;
use SegmentGenerator\Entities\ChapterCollection;
use SegmentGenerator\Entities\SegmentCollection;
use SegmentGenerator\Entities\Silence;

/**
 * Segments silences by generated chapters.
 */
class SilenceSegmentatorByChapters implements SilenceSegmentator
{
    /**
     * An instance of ChapterGenerator.
     *
     * @var ChapterGenerator
     */
    protected $generator;

    /**
     * An instance of ChapterSegmentator.
     *
     * @var ChapterSegmentator
     */
    protected $segmentator;

    /**
     * A logger.
     *
     * @var Logger
     */
    protected $logger;

    /**
     * The last generated chapters.
     *
     * @var ChapterCollection|null
     */
    private $chapters;

    public function __construct(ChapterGenerator $generator, ChapterSegmentator $segmentator, Logger $logger)
    {
        $this->generator = $generator;
        $this->segmentator = $segmentator;
        $this->logger = $logger;
    }

    /**
     * Returns a chapter generator
     *
     * @return ChapterGenerator
     */
    public function getChapterGenerator(): ChapterGenerator
    {
        return $this->generator;
    }

    /**
     * Returns a chapter segmentator.
     *
     * @return ChapterSegmentator
     */
    public function getChapterSegmentator(): ChapterSegmentator
    {
        return $this->segmentator;
    }

    /**
     * Segments the given silences.
     *
     * @param Silence[] $silences
     * @return SegmentCollection
     */
    public function segment(array $silences): SegmentCollection
    {
        $this->chapters = $this->generator->fromSilences($silences);
        // Generates template titles.
        $this->chapters->fillTitles();
        $segments = $this->segmentator->segment($this->chapters);

        $this->log();

        return $segments;
    }

    /**
     * Returns the last generated chapters.
     *
     * @return ChapterCollection|null
     */
    public function getChapters(): ?ChapterCollection
    {
        return $this->chapters;
    }

    /**
     * Logs data of the chapters.
     *
     * @return void
     */
    private function log(): void
    {
        $this->logger->log("A number of the chapters: %d.\n", $this->chapters->getNumberOfChapters());
        $this->logger->log("A number of the parts of the chapters: %d.\n", $this->chapters->getNumberOfParts());
        $this->logger->log("A duration of the chapters without silences between chapters: %dms.\n", $this->chapters->getDuration());
    }
}
