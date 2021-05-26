<?php

namespace SegmentGenerator\ChapterGenerators;

use SegmentGenerator\Contracts\ChapterGenerator;
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
     * A describer.
     *
     * @var DescriberOfChapterGenerator
     */
    protected $describer;

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

    public function __construct(SilenceAnalyzer $analyzer, DescriberOfChapterGenerator $describer)
    {
        $this->analyzer = $analyzer;
        $this->describer = $describer;
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
     * Returns a describer.
     *
     * @return DescriberOfChapterGenerator
     */
    public function getDescriber(): DescriberOfChapterGenerator
    {
        return $this->describer;
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
        $this->describer
            ->setSilenceIndex($silenceIndex)
            ->describeSilence($silence)
        ;

        if ($this->analyzer->isTransition($silence)) {
            $this->describer->describeTransition($silence);
            $this->finishChapter($silence);

            return self::TRANSITION;
        }

        $this->describer->describePause($silence);
        $this->nextPart($silence);

        return self::NOT_TRANSITION;
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
        $this->chapterIndex = 0;
    }
}
