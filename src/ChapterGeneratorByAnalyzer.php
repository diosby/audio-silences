<?php

namespace SegmentGenerator;

use SegmentGenerator\Contracts\ChapterAnalyzer;
use SegmentGenerator\Contracts\ChapterGenerator as GeneratorInterface;

/**
 * Makes chapters by silences.
 */
class ChapterGeneratorByAnalyzer implements GeneratorInterface
{
    use DebugLog;

    protected $analyzer;

    /**
     * Chapters of the last generation.
     *
     * @var Chapter[]
     */
    private $chapters = [];

    /**
     * An index of the last generation.
     *
     * @var int
     */
    private $chapterIndex = 0;

    public function __construct(ChapterAnalyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    /**
     * Generates a chapter collection from the given silences.
     *
     * @param Silence[] $silences
     * @return ChapterCollection
     */
    public function fromSilences(iterable $silences): ChapterCollection
    {
        $this->chapters = [];
        $this->chapterIndex = 0;

        foreach ($silences as $key => $silence) {
            $index = $key + 1;
            $this->info($index, $silence);

            if ($this->analyzer->isTransition($silence)) {
                $this->transition($index, $silence);
                $this->finishChapter($silence);
            } else {
                $this->pause($index, $silence);
                $this->next($silence);
            }
        }

        return new ChapterCollection($this->chapters);
    }

    /**
     * Adds a start offset to the last chapter and a finish offset to a new
     * chapter.
     *
     * @param Silence $silence
     * @return void
     */
    protected function finishChapter(Silence $silence): void
    {
        // Adds the finish time to the last chapter.
        $this->getChapter()->finishBySilence($silence);
        $this->nextChapter();
        // Adds the start time to the new chapter.
        $this->getChapter()->startBySilence($silence);
    }

    /**
     * Adds a finish time to the last segment and a start time to a new segment.
     *
     * @param Silence $silence
     * @return void
     */
    protected function next(Silence $silence): void
    {
        $this->getChapter()->plusBySilence($silence);
    }

    /**
     * Returns an existent chapter or creates a new.
     *
     * @return Chapter
     */
    protected function getChapter(): Chapter
    {
        if (!isset($this->chapters[$this->chapterIndex])) {
            $this->chapters[$this->chapterIndex] = new Chapter;
        }

        return $this->chapters[$this->chapterIndex];
    }

    /**
     * Moves the pointer to the next chapter.
     *
     * @return void
     */
    protected function nextChapter(): void
    {
        $this->chapterIndex++;
    }

    /**
     * Shows info about the given silence.
     *
     * @param int $index
     * @param Silence $silence
     * @return void
     */
    protected function info(int $index, Silence $silence): void
    {
        $this->log(
            "Silence %d, ms: %d, from %s until %s.\n",
            $index,
            $silence->getDuration(),
            (string) $silence->getFrom(),
            (string) $silence->getUntil()
        );
    }

    /**
     * Shows info about a transition of the silence.
     *
     * @param int $index
     * @param Silence $silence
     * @return void
     */
    protected function transition(int $index, Silence $silence): void
    {
        $this->log("Silence %d is the transition: %d.\n", $index, $silence->getDuration());
    }

    /**
     * Shows info about a pause of the silence.
     *
     * @param int $index
     * @param Silence $silence
     * @return void
     */
    protected function pause(int $index, Silence $silence): void
    {
        $this->log("Silence %d is the transition: %d.\n", $index, $silence->getDuration());
    }
}
