<?php

namespace SegmentGenerator\SilenceSegmentators;

use SegmentGenerator\Contracts\ChapterGenerator;
use SegmentGenerator\Contracts\ChapterSegmentator;
use SegmentGenerator\Entities\ChapterCollection;
use SegmentGenerator\Entities\SegmentCollection;
use SegmentGenerator\Entities\Silence;
use SegmentGenerator\SilenceSegmentators\Contracts\SilenceSegmentatorWithChapters;

/**
 * Segments silences by generated chapters.
 */
class SilenceSegmentatorByChapters implements SilenceSegmentatorWithChapters
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
}
