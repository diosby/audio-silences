<?php

namespace SegmentGenerator\ChapterGenerators;

use SegmentGenerator\Contracts\ChapterAnalyzer;
use SegmentGenerator\Contracts\ChapterGenerator as GeneratorInterface;
use SegmentGenerator\Entities\Chapter;
use SegmentGenerator\Entities\ChapterCollection;
use SegmentGenerator\Entities\Silence;

/**
 * Makes chapters by silences.
 */
class ChapterGeneratorByAnalyzer implements GeneratorInterface
{
    /**
     * An analyzer.
     *
     * @var ChapterAnalyzer
     */
    protected $analyzer;

    /**
     * Chapters of the last generation.
     *
     * @var Chapter[]
     */
    private $chapters = [];

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

        foreach ($silences as $key => $silence) {
            if ($this->analyzer->isTransition($silence)) {
                $this->finishChapter($silence);
            } else {
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
}
