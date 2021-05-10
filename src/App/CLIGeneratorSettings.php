<?php

namespace SegmentGenerator\App;

use SegmentGenerator\Contracts\GeneratorSettings;

class CLIGeneratorSettings implements GeneratorSettings
{
    /**
     * A source file.
     *
     * @var string
     */
    private $source;

    /**
     * A chapter transition. It is a silence duration which reliably indicates a chapter transition.
     *
     * @var int|null
     */
    private $transition;

    /**
     * A minimal silence between parts (segments) in a chapter which can be used to split a long chapter
     *
     * @var int|null
     */
    private $minSilence;

    /**
     * A duration of a segment in multiple segments after which the chapter will be broken up.
     *
     * @var int|null
     */
    private $maxDuration;

    /**
     * An output file with the result.
     *
     * @var string|null
     */
    private $output;

    /**
     * A debug mode.
     *
     * @var bool
     */
    private $debug;

    public function __construct()
    {
        // Expected arguments of CLI.
        $shortopts = '';
        $longopts = [];

        // A file path to XML with silences.
        $shortopts .= 's:';
        $longopts[] = 'source:';

        // A chapter transition.
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

        if (!is_readable($this->source)) {
            exit("The $this->source file isn't readable.\n");
        }

        if (!is_file($this->source)) {
            exit("The $this->source file isn't a file.\n");
        }
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getTransition(): ?int
    {
        return $this->transition;
    }

    public function getMinSilence(): ?int
    {
        return $this->minSilence;
    }

    public function getMaxSegment(): ?int
    {
        return $this->maxDuration;
    }

    public function getOutput(): ?string
    {
        return $this->output;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }
}
