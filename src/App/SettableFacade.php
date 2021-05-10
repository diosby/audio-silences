<?php

namespace SegmentGenerator\App;

use SegmentGenerator\ChapterGenerators\ChapterGeneratorByAnalyzer;
use SegmentGenerator\ChapterSegmentators\ChapterSegmentator;
use SegmentGenerator\Contracts\GeneratorSettings;
use SegmentGenerator\Contracts\Outputter;
use SegmentGenerator\Contracts\SegmentGeneratorFacade;
use SegmentGenerator\Contracts\SilenceSegmentator;
use SegmentGenerator\Entities\Interval;
use SegmentGenerator\Entities\Silence;
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

    public function __construct(GeneratorSettings $settings)
    {
        $this->settings = $settings;
    }

    function getSilences(): iterable
    {
        $xml = simplexml_load_file($this->settings->getSource());
        /** @var Silence[] $silences */
        $silences = [];

        foreach ($xml as $item) {
            $silences[] = new Silence(new Interval($item['from']), new Interval($item['until']));
        }

        return $silences;
    }

    function getSegmentator(): SilenceSegmentator
    {
        $analyzer = new SilenceAnalyzerByMinTransition($this->settings->getTransition());
        $chapterGenerator = new ChapterGeneratorByAnalyzer($analyzer);
        $chapterSegmentator = new ChapterSegmentator($this->settings->getMaxSegment(), $this->settings->getMinSilence());
        $silenceSegmentator = new SilenceSegmentatorByChapters($chapterGenerator, $chapterSegmentator);

        return $silenceSegmentator;
    }

    public function getOutputter(): Outputter
    {
        $decorators = new JsonDecorator(new ArrayWrapperDecorator(new ArrayBase()));

        if ($this->settings->getOutput()) {
            $outputter = new FileOutputter($this->settings->getOutput(), $decorators);
        } else {
            $outputter = new StdOutputter($decorators);
        }

        return $outputter;
    }
}
