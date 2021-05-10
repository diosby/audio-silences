<?php

namespace SegmentGenerator\Outputters\Decorators;

use SegmentGenerator\Contracts\Outputter;
use SegmentGenerator\Entities\SegmentCollection;

/**
 * Outputs segments through an outputter.
 */
abstract class OutputterDecorator implements Outputter
{
    /**
     * An outputter.
     *
     * @var Outputter
     */
    protected $outputter;

    /**
     * Initializes a decorator with the given outputter.
     *
     * @param Outputter $outputter
     */
    public function __construct(Outputter $outputter)
    {
        $this->outputter = $outputter;
    }

    /**
     * Outputs the given segments.
     *
     * @param SegmentCollection $segments
     * @return void
     */
    public function output(SegmentCollection $segments): void
    {
        $this->outputter->output($segments);
    }

    /**
     * Returns the outputter.
     *
     * @return Outputter
     */
    public function getOutputter(): Outputter
    {
        return $this->outputter;
    }
}
