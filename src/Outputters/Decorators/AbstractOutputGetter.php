<?php

namespace SegmentGenerator\Outputters\Decorators;

use SegmentGenerator\Outputters\Contracts\OutputGetter;

/**
 * Extends the OutputterDecorator class and returns an output through
 * the `OutputGetter::getOutput()` method.
 *
 * @property OutputGetter $outputter An outputter with getter.
 */
abstract class AbstractOutputGetter extends OutputterDecorator implements OutputGetter
{
    /**
     * An output of the last segmentation of the outputter.
     *
     * @var mixed|null
     */
    protected $output;

    /**
     * Initializes an instance with the given output getter.
     *
     * @param OutputGetter $outputter
     */
    public function __construct(OutputGetter $outputter)
    {
        parent::__construct($outputter);
    }

    /**
     * Returns an output of the outputter.
     *
     * @return mixed|null
     */
    public function getOutput()
    {
        return $this->output;
    }
}
