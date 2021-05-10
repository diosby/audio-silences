<?php

namespace SegmentGenerator\Outputters;

use SegmentGenerator\Contracts\Outputter;
use SegmentGenerator\Entities\SegmentCollection;
use SegmentGenerator\Outputters\Contracts\StringOutputterGetter;

/**
 * Outputs segments to `stdout`. Works only with the `StringOutputterGetter`
 * interface.
 */
class StdOutputter implements Outputter
{
    /**
     * A string outputter.
     *
     * @var StringOutputterGetter
     */
    protected $outputter;

    /**
     * Initializes an outputter to show segments to `stdout` through a string
     * outputter.
     *
     * @param StringOutputterGetter $outputter
     */
    public function __construct(StringOutputterGetter $outputter)
    {
        $this->outputter = $outputter;
    }

    /**
     * Outputs the given segments to `stdout`.
     *
     * @param SegmentCollection $segments
     * @return void
     */
    public function output(SegmentCollection $segments): void
    {
        $this->outputter->output($segments);
        $this->show();
    }

    /**
     * Shows the output to `stdout`.
     *
     * @return void
     */
    protected function show(): void
    {
        print($this->outputter->getOutput()."\n");
    }
}
