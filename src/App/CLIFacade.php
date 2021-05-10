<?php

namespace SegmentGenerator\App;

use SegmentGenerator\ChapterGenerators\ChapterGeneratorByAnalyzer;
use SegmentGenerator\ChapterSegmentators\ChapterSegmentator;
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

class CLIFacade implements SegmentGeneratorFacade
{
    private $source;

    private $transition;

    private $minSilence;

    private $maxDuration;

    private $output;

    private $debug;

    public function __construct()
    {
        // Expected arguments of CLI.
        $shortopts = '';
        $longopts = [];

        // A file path to XML with silences.
        $shortopts .= 's:';
        $longopts[] = 'source:';

        // A chapter transition. It is a silence duration which reliably indicates a chapter transition.
        $shortopts .= 't:';
        $longopts[] = 'transition:';

        // A minimal silence between parts (segments) in a chapter which can be used to split a long chapter.
        $shortopts .= 'm:';
        $longopts[] = 'min-silence:';

        // A duration of a segment in multiple segments after which the chapter will be broken up.
        $shortopts .= 'd:';
        $longopts[] = 'max-duration:';

        // An output file with the result.
        $shortopts .= 'o:';
        $longopts[] = 'output:';

        // A debug mode. It is used to show the processing.
        $longopts[] = 'debug::';

        $options = getopt($shortopts, $longopts);

        // Gets arguments.
        $this->source = $options['source'] ?? $options['s'] ?? null;
        $this->transition = $options['transition'] ?? $options['t'] ?? null;
        $this->minSilence = $options['min-silence'] ?? $options['m'] ?? null;
        $this->maxDuration = $options['max-duration'] ?? $options['d'] ?? null;
        $this->output = $options['output'] ?? $options['o'] ?? null;
        $this->debug = isset($options['debug']) ? empty($options['debug']) : false;

        if (empty($this->source)) {
            exit("The path to a file wasn't given. Set the path to a file through --source <path> or -s <path>.\n");
        }

        if (empty($this->transition)) {
            exit("The chapter transition wasn't given. Set the transition through --transition <duration> or -t <duration>. The duration should be greater than zero.\n");
        } elseif (!is_numeric($this->transition)) {
            exit("The given transition isn't a number. The value should be an integer.\n");
        }

        if (!file_exists($this->source)) {
            exit("The $this->source file doesn't exist.\n");
        }
    }

    function getSilences(): iterable
    {
        if (!file_exists($this->source)) {
            exit("The $this->source file doesn't exist.\n");
        }

        $xml = simplexml_load_file($this->source);
        /** @var Silence[] $silences */
        $silences = [];

        foreach ($xml as $item) {
            $silences[] = new Silence(new Interval($item['from']), new Interval($item['until']));
        }

        return $silences;
    }

    function getSegmentator(): SilenceSegmentator
    {
        $analyzer = new SilenceAnalyzerByMinTransition($this->transition);
        $chapterGenerator = new ChapterGeneratorByAnalyzer($analyzer);
        $chapterSegmentator = new ChapterSegmentator($this->maxDuration, $this->minSilence);
        $silenceSegmentator = new SilenceSegmentatorByChapters($chapterGenerator, $chapterSegmentator);

        return $silenceSegmentator;
    }

    public function getOutputter(): Outputter
    {
        $decorators = new JsonDecorator(new ArrayWrapperDecorator(new ArrayBase()));

        if (isset($this->output)) {
            $outputter = new FileOutputter($this->output, $decorators);
        } else {
            $outputter = new StdOutputter($decorators);
        }

        return $outputter;
    }
}
