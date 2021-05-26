<?php

namespace SegmentGenerator\ChapterSegmentators;

use SegmentGenerator\ChapterSegmentators\Contracts\DescriberOfChapterSegmentator;
use SegmentGenerator\ChapterSegmentators\Contracts\SegmentTitleMaker;
use SegmentGenerator\Contracts\ChapterSegmentator as SegmentatorInterface;
use SegmentGenerator\Entities\Chapter;
use SegmentGenerator\Entities\ChapterCollection;
use SegmentGenerator\Entities\SegmentCollection;

/**
 * Segments chapters.
 */
class ChapterSegmentator implements SegmentatorInterface
{
    /**
     * A describer.
     *
     * @var DescriberOfChapterSegmentator
     */
    protected $describer;

    /**
     * A chapter analyzer.
     *
     * @var ChapterAnalyzer
     */
    protected $chapterAnalyzer;

    /**
     * A title maker.
     *
     * @var SegmentTitleMaker
     */
    protected $titleMaker;

    /**
     * A multiple chapter segmentator.
     *
     * @var MultipleChapterSegmentator
     */
    protected $multipleChapterSegmentator;

    /**
     * Segments of the handled chapters.
     *
     * @var SegmentCollection
     */
    protected $segments;

    public function __construct(
        ChapterAnalyzer $chapterAnalyzer,
        DescriberOfChapterSegmentator $describer,
        MultipleChapterSegmentator $multipleChapterSegmentator,
        SegmentTitleMaker $titleMaker
    ) {
        $this->chapterAnalyzer = $chapterAnalyzer;
        $this->describer = $describer;
        $this->multipleChapterSegmentator = $multipleChapterSegmentator;
        $this->titleMaker = $titleMaker;
    }

    /**
     * Returns a chapter analyzer.
     *
     * @return ChapterAnalyzer
     */
    public function getChapterAnalyzer(): ChapterAnalyzer
    {
        return $this->chapterAnalyzer;
    }

    /**
     * Returns a describer.
     *
     * @return DescriberOfChapterSegmentator
     */
    public function getDescriber(): DescriberOfChapterSegmentator
    {
        return $this->describer;
    }

    /**
     * Returns a segment title maker.
     *
     * @return SegmentTitleMaker
     */
    public function getTitleMaker(): SegmentTitleMaker
    {
        return $this->titleMaker;
    }

    /**
     * Segments the given chapters.
     *
     * @param ChapterCollection $chapters
     * @return SegmentCollection
     */
    public function segment(ChapterCollection $chapters): SegmentCollection
    {
        $this->segments = new SegmentCollection();
        $this->describer->describerChapters($chapters);

        foreach ($chapters->getItems() as $chapterIndex => $chapter) {
            $this->describer->setChapterIndex($chapterIndex);
            $this->describer->describerChapter($chapter);
            $this->segmentChapter($chapter);
        }

        $this->describer->describeSegments($this->segments);

        return $this->segments;
    }

    /**
     * Segments the given chapter.
     *
     * @param Chapter $chapter
     * @return void
     */
    protected function segmentChapter(Chapter $chapter): void
    {
        if ($this->isUnbreakable($chapter)) {
            $this->pushFullChapter($chapter);
        } else {
            $this->pushMultipleChapter($chapter);
        }
    }

    /**
     * Checks whether the given chapter is unbreakable.
     *
     * @param Chapter $chapter
     * @return bool
     */
    public function isUnbreakable(Chapter $chapter): bool
    {
        return $this->chapterAnalyzer->isUnbreakable($chapter);
    }

    /**
     * Pushes the given full chapter.
     *
     * @param Chapter $chapter
     * @return void
     */
    protected function pushFullChapter(Chapter $chapter): void
    {
        $this->describer->describeFullChapter($chapter);
        $this->segments->add($chapter->getOffset(), $this->titleMaker->fromChapter($chapter));
    }

    /**
     * Pushes the given multiple chapter.
     *
     * @param Chapter $chapter
     * @return void
     */
    protected function pushMultipleChapter(Chapter $chapter): void
    {
        $this->multipleChapterSegmentator->segment($chapter, $this->segments);
    }
}
