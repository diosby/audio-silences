<?php

namespace SegmentGenerator\ChapterGenerators;

use SegmentGenerator\Contracts\ChapterGenerator;
use SegmentGenerator\Contracts\Logger;
use SegmentGenerator\Contracts\SilenceAnalyzer;
use SegmentGenerator\Entities\Chapter;
use SegmentGenerator\Entities\ChapterCollection;
use SegmentGenerator\Entities\Silence;

/**
 * Makes chapters by silences.
 */
class ChapterGeneratorByAnalyzer implements ChapterGenerator
{
    /**
     * The state of a handling silence. The silence is a transition.
     */
    public const TRANSITION = 1;

    /**
     * The state of a handling silence. The silence isn't a transition.
     */
    public const NOT_TRANSITION = 0;

    /**
     * An analyzer.
     *
     * @var SilenceAnalyzer
     */
    protected $analyzer;

    /**
     * A logger.
     *
     * @var Logger
     */
    protected $logger;

    /**
     * A chapter index of the current handling.
     *
     * @var int
     */
    protected $chapterIndex = 0;

    /**
     * Chapters of the last generation.
     *
     * @var Chapter[]
     */
    private $chapters = [];

    public function __construct(SilenceAnalyzer $analyzer, Logger $logger)
    {
        $this->analyzer = $analyzer;
        $this->logger = $logger;
    }

    /**
     * Returns a silence analyzer.
     *
     * @return SilenceAnalyzer
     */
    public function getAnalyzer(): SilenceAnalyzer
    {
        return $this->analyzer;
    }

    /**
     * Returns a logger.
     *
     * @return Logger
     */
    public function getLogger(): Logger
    {
        return $this->logger;
    }

    /**
     * Generates a chapter collection from the given silences.
     *
     * @param Silence[] $silences
     * @return ChapterCollection
     */
    public function fromSilences(iterable $silences): ChapterCollection
    {
        $this->reset();
        array_walk($silences, [$this, 'handleSilence']);

        return new ChapterCollection($this->chapters);
    }

    /**
     * Handles the given silence.
     * Returns 1 if the silence is a transition. In other cases returns 0.
     *
     * @param Silence $silence
     * @param int|string $silenceIndex
     * @return int
     */
    public function handleSilence(Silence $silence, $silenceIndex): int
    {
        $this->describeSilence($silence, $silenceIndex);

        if ($this->analyzer->isTransition($silence)) {
            $this->describeTransition($silence, $silenceIndex);
            $this->finishChapter($silence);

            return self::TRANSITION;
        }

        $this->describePause($silence, $silenceIndex);
        $this->nextPart($silence);

        return self::NOT_TRANSITION;
    }

    /**
     * Describes the given silence.
     *
     * @param Silence $silence
     * @param int|string $silenceIndex
     * @return void
     */
    protected function describeSilence(Silence $silence, $silenceIndex): void
    {
        $this->logger->log(
            "Silence #%s: %d ms, from %s until %s.\n",
            $silenceIndex,
            $silence->getDuration(),
            (string) $silence->getFrom(),
            (string) $silence->getUntil()
        );
    }

    /**
     * Describes the given transition.
     *
     * @param Silence $silence
     * @param int|string $silenceIndex
     * @return void
     */
    protected function describeTransition(Silence $silence, $silenceIndex): void
    {
        $this->logger->log(
            "Silence #%s is the transition: %d ms.\n",
            $silenceIndex,
            $silence->getDuration()
        );
    }

    /**
     * Describes the given pause.
     *
     * @param Silence $silence
     * @param int|string $silenceIndex
     * @return void
     */
    protected function describePause(Silence $silence, $silenceIndex): void
    {
        $this->logger->log(
            "Silence #%s is the pause: %d ms.\n",
            $silenceIndex,
            $silence->getDuration()
        );
    }

    /**
     * Returns an existent last chapter or creates a new and returns it.
     *
     * @return Chapter
     */
    public function getChapter(): Chapter
    {
        if (!isset($this->chapters[$this->chapterIndex])) {
            $this->chapters[$this->chapterIndex] = new Chapter;
        }

        return $this->chapters[$this->chapterIndex];
    }

    /**
     * Adds a start offset to the last chapter and a finish offset to a new
     * chapter.
     *
     * @param Silence $silence
     * @return void
     */
    public function finishChapter(Silence $silence): void
    {
        // Adds the finish time to the last chapter.
        $this->getChapter()->finishBySilence($silence);
        // Adds the start time to the new chapter.
        $this->nextChapter()->startBySilence($silence);
    }

    /**
     * Adds a finish time to the last segment and a start time to a new segment.
     *
     * @param Silence $silence
     * @return void
     */
    public function nextPart(Silence $silence): void
    {
        $this->getChapter()->plusBySilence($silence);
    }

    /**
     * Moves to the next chapter and returns the new chapter.
     *
     * @return Chapter
     */
    public function nextChapter(): Chapter
    {
        $this->chapterIndex++;

        return $this->getChapter();
    }

    /**
     * Resets the pointer and chapters.
     *
     * @return void
     */
    protected function reset(): void
    {
        $this->chapters = [];
        $this->silenceIndex = 0;
        $this->chapterIndex = 0;
    }
}
