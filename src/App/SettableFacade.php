<?php

namespace SegmentGenerator\App;

use SegmentGenerator\ChapterGenerators\ChapterGeneratorByAnalyzer;
use SegmentGenerator\ChapterGenerators\DescriberOfChapterGenerator;
use SegmentGenerator\ChapterGenerators\DescriberOfChapterGeneratorWithLogger;
use SegmentGenerator\ChapterSegmentators\ChapterAnalyzer;
use SegmentGenerator\ChapterSegmentators\ChapterSegmentator;
use SegmentGenerator\ChapterSegmentators\ChapterSegmentatorDescriber;
use SegmentGenerator\ChapterSegmentators\Contracts\DescriberOfChapterSegmentator;
use SegmentGenerator\ChapterSegmentators\Contracts\DescriberOfMultipleChapterSegmentator;
use SegmentGenerator\ChapterSegmentators\Contracts\MultipleChapterSegmentator as MultipleChapterSegmentatorInterface;
use SegmentGenerator\ChapterSegmentators\Contracts\SegmentTitleMaker as SegmentTitleMakerInterface;
use SegmentGenerator\ChapterSegmentators\MultipleChapterSegmentator;
use SegmentGenerator\ChapterSegmentators\SegmentTitleMaker;
use SegmentGenerator\Contracts\ChapterGenerator;
use SegmentGenerator\Contracts\ChapterSegmentator as ChapterSegmentatorInterface;
use SegmentGenerator\Contracts\GeneratorSettings;
use SegmentGenerator\Contracts\Logger;
use SegmentGenerator\Contracts\Outputter;
use SegmentGenerator\Contracts\SegmentGeneratorFacade;
use SegmentGenerator\Contracts\SilenceAnalyzer;
use SegmentGenerator\Contracts\SilenceSegmentator;
use SegmentGenerator\Entities\Interval;
use SegmentGenerator\Entities\Silence;
use SegmentGenerator\Loggers\NullLogger;
use SegmentGenerator\Loggers\ScreenLogger;
use SegmentGenerator\Outputters\Contracts\StringOutputterGetter;
use SegmentGenerator\Outputters\Decorators\ArrayBase;
use SegmentGenerator\Outputters\Decorators\ArrayWrapperDecorator;
use SegmentGenerator\Outputters\Decorators\JsonDecorator;
use SegmentGenerator\Outputters\FileOutputter;
use SegmentGenerator\Outputters\StdOutputter;
use SegmentGenerator\SilenceAnalyzers\SilenceAnalyzerByMinTransition;
use SegmentGenerator\SilenceSegmentators\SilenceSegmentatorByChapters;

class SettableFacade implements SegmentGeneratorFacade
{
    /**
     * Settings of the generator.
     *
     * @var GeneratorSettings
     */
    private $settings;

    /**
     * Read silences.
     *
     * @var Silence[]
     */
    private $silences;

    /**
     * A silence segmentator.
     *
     * @var SilenceSegmentator
     */
    private $segmentator;

    /**
     * A chapter generator.
     *
     * @var ChapterGenerator
     */
    private $chapterGenerator;

    /**
     * A chapter segmentator.
     *
     * @var ChapterSegmentator
     */
    private $chapterSegmentator;

    /**
     * A multiple chapter segmentator.
     *
     * @var MultipleChapterSegmentatorInterface
     */
    private $multipleChapterSegmentator;

    /**
     * A chapter analyzer.
     *
     * @var ChapterAnalyzer
     */
    private $chapterAnalyzer;

    /**
     * A chapter describer.
     *
     * @var ChapterSegmentatorDescriber
     */
    private $chapterDescriber;

    /**
     * A title maker.
     *
     * @var SegmentTitleMakerInterface
     */
    private $titleMaker;

    /**
     * An outputter.
     *
     * @var Outputter
     */
    private $outputter;

    /**
     * A logger.
     *
     * @var Logger
     */
    private $logger;

    /**
     * A string outputter getter.
     *
     * @var StringOutputterGetter
     */
    private $stringOutputterGetter;

    public function __construct(GeneratorSettings $settings)
    {
        $this->settings = $settings;
        $this->logger = $settings->isDebug() ? new ScreenLogger : new NullLogger;
    }

    /**
     * Returns an iterator of silences.
     *
     * @return Silence[]
     */
    function getSilences(): iterable
    {
        if (!isset($this->silences)) {
            $this->silences = $this->newSilences();
        }

        return $this->silences;
    }

    /**
     * Reads and initializes silences.
     *
     * @return iterable
     */
    protected function newSilences(): iterable
    {
        $xml = simplexml_load_file($this->settings->getSource());
        $silences = [];

        foreach ($xml as $item) {
            $silences[] = new Silence(new Interval($item['from']), new Interval($item['until']));
        }

        return $silences;
    }

    /**
     * Returns a silence segmentator.
     *
     * @return SilenceSegmentator
     */
    public function getSegmentator(): SilenceSegmentator
    {
        if (!isset($this->segmentator)) {
            $this->segmentator = $this->newSegmentator();
        }

        return $this->segmentator;
    }

    /**
     * Initializes a new silence segmentator.
     *
     * @return SilenceSegmentator
     */
    protected function newSegmentator(): SilenceSegmentator
    {
        return new SilenceSegmentatorByChapters(
            $this->getChapterGenerator(),
            $this->getChapterSegmentator(),
            $this->logger
        );
    }

    /**
     * Returns a chapter generator.
     *
     * @return ChapterGenerator
     */
    protected function getChapterGenerator(): ChapterGenerator
    {
        if (!isset($this->chapterGenerator)) {
            $this->chapterGenerator = $this->newChapterGenerator();
        }

        return $this->chapterGenerator;
    }

    /**
     * Initializes a new chapter generator.
     *
     * @return ChapterGenerator
     */
    protected function newChapterGenerator(): ChapterGenerator
    {
        return new ChapterGeneratorByAnalyzer(
            $this->getSilenceAnalyzer(),
            $this->getDescriberOfChapterGenerator()
        );
    }

    /**
     * Initializes a new silence analyzer.
     *
     * @return SilenceAnalyzer
     */
    protected function getSilenceAnalyzer(): SilenceAnalyzer
    {
        return new SilenceAnalyzerByMinTransition($this->settings->getTransition());
    }

    /**
     * Initializes a new describer.
     *
     * @return DescriberOfChapterGenerator
     */
    protected function getDescriberOfChapterGenerator(): DescriberOfChapterGenerator
    {
        return new DescriberOfChapterGeneratorWithLogger($this->logger);
    }

    /**
     * Returns a chapter segmentator.
     *
     * @return ChapterSegmentatorInterface
     */
    protected function getChapterSegmentator(): ChapterSegmentatorInterface
    {
        if (!isset($this->chapterSegmentator)) {
            $this->chapterSegmentator = $this->newChapterSegmentator();
        }

        return $this->chapterSegmentator;
    }

    /**
     * Initializes a new chapter segmentator.
     *
     * @return ChapterSegmentatorInterface
     */
    public function newChapterSegmentator(): ChapterSegmentatorInterface
    {
        return new ChapterSegmentator(
            $this->getChapterAnalyzer(),
            $this->getDescriberOfChapterSegmentator(),
            $this->getMultipleChapterSegmentator(),
            $this->getTitleMaker()
        );
    }

    /**
     * Returns a multiple chapter segmentator.
     *
     * @return MultipleChapterSegmentatorInterface
     */
    public function getMultipleChapterSegmentator(): MultipleChapterSegmentatorInterface
    {
        if (!isset($this->multipleChapterSegmentator)) {
            $this->multipleChapterSegmentator = $this->newMultipleChapterSegmentator();
        }

        return $this->multipleChapterSegmentator;
    }

    /**
     * Initializes a new multiple chapter segmentator.
     *
     * @return MultipleChapterSegmentatorInterface
     */
    protected function newMultipleChapterSegmentator(): MultipleChapterSegmentatorInterface
    {
        return new MultipleChapterSegmentator(
            $this->getChapterAnalyzer(),
            $this->getTitleMaker(),
            $this->getDescriberOfMultipleChapterSegmentator()
        );
    }

    /**
     * Returns a chapter analyzer.
     *
     * @return ChapterAnalyzer
     */
    public function getChapterAnalyzer(): ChapterAnalyzer
    {
        if (!isset($this->chapterAnalyzer)) {
            $this->chapterAnalyzer = $this->newChapterAnalyzer();
        }

        return $this->chapterAnalyzer;
    }

    /**
     * Initializes a new chapter analyzer.
     *
     * @return ChapterAnalyzer
     */
    protected function newChapterAnalyzer(): ChapterAnalyzer
    {
        return new ChapterAnalyzer(
            $this->settings->getMaxSegment(),
            $this->settings->getMinSilence()
        );
    }

    /**
     * Returns a describer of a chapter segmentator.
     *
     * @return DescriberOfChapterSegmentator
     */
    public function getDescriberOfChapterSegmentator(): DescriberOfChapterSegmentator
    {
        return $this->getChapterDescriber();
    }

    /**
     * Returns a multiple chapter describer.
     *
     * @return DescriberOfMultipleChapterSegmentator
     */
    public function getDescriberOfMultipleChapterSegmentator(): DescriberOfMultipleChapterSegmentator
    {
        return $this->getChapterDescriber();
    }

    /**
     * Returns a chapter describer.
     *
     * @return ChapterSegmentatorDescriber
     */
    public function getChapterDescriber(): ChapterSegmentatorDescriber
    {
        if (!isset($this->chapterDescriber)) {
            $this->chapterDescriber = $this->newChapterDescriber();
        }

        return $this->chapterDescriber;
    }

    /**
     * Initializes a new chapter describer.
     *
     * @return ChapterSegmentatorDescriber
     */
    protected function newChapterDescriber(): ChapterSegmentatorDescriber
    {
        return new ChapterSegmentatorDescriber($this->logger);
    }

    /**
     * Returns a title maker.
     *
     * @return SegmentTitleMakerInterface
     */
    public function getTitleMaker(): SegmentTitleMakerInterface
    {
        if (!isset($this->titleMaker)) {
            $this->titleMaker = $this->newTitleMaker();
        }

        return $this->titleMaker;
    }

    /**
     * Initializes a new title maker.
     *
     * @return SegmentTitleMakerInterface
     */
    protected function newTitleMaker(): SegmentTitleMakerInterface
    {
        return new SegmentTitleMaker;
    }

    /**
     * Returns an outputter.
     *
     * @return Outputter
     */
    public function getOutputter(): Outputter
    {
        if (!isset($this->outputter)) {
            $this->outputter = $this->newOutputter();
        }

        return $this->outputter;
    }

    /**
     * Initializes a new outputter.
     *
     * @return Outputter
     */
    protected function newOutputter(): Outputter
    {
        if ($this->settings->getOutput()) {
            return new FileOutputter($this->settings->getOutput(), $this->getStringOutputterGetter());
        }

        return new StdOutputter($this->getStringOutputterGetter());
    }

    /**
     * Returns a string outputter getter.
     *
     * @return StringOutputterGetter
     */
    protected function getStringOutputterGetter(): StringOutputterGetter
    {
        if (!isset($this->stringOutputterGetter)) {
            $this->stringOutputterGetter = $this->newStringOutputterGetter();
        }

        return $this->stringOutputterGetter;
    }

    /**
     * Initializes a new string outputter getter.
     *
     * @return StringOutputterGetter
     */
    protected function newStringOutputterGetter(): StringOutputterGetter
    {
        return new JsonDecorator(new ArrayWrapperDecorator(new ArrayBase()));
    }
}
