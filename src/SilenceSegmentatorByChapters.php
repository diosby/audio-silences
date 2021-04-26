<?php

namespace SegmentGenerator;

use SegmentGenerator\Contracts\ChapterGenerator;
use SegmentGenerator\Contracts\ChapterSegmentator;
use SegmentGenerator\Contracts\SilenceSegmentator;
use SegmentGenerator\SegmentCollection;
use SegmentGenerator\Silence;

/**
 * Segments silences by generated chapters.
 */
class SilenceSegmentatorByChapters implements SilenceSegmentator
{
    use DebugLog {
        debugMode as setDebugMode;
    }

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
     * The last generated chapters.
     *
     * @var ChapterCollection|null
     */
    private $chapters;

    public function __construct(ChapterGenerator $generator, ChapterSegmentator $segmentator)
    {
        $this->generator = $generator;
        $this->segmentator = $segmentator;
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
     * Sets a state of the debugging.
     *
     * @param bool $state
     * @return void
     */
    public function debugMode(bool $state = true): void
    {
        $this->setDebugMode($state);
        $this->generator->debugMode($state);
        $this->segmentator->debugMode($state);
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

        $this->info();

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
     * Shows the debug info.
     *
     * @return void
     */
    public function info(): void
    {
        $this->log("A number of the chapters: %d.\n", $this->chapters->getNumberOfChapters());
        $this->log("A number of the parts of the chapters: %d.\n", $this->chapters->getNumberOfParts());
        $this->log("A duration of the chapters without silences between chapters: %dms.\n", $this->chapters->getDuration());
    }
}
