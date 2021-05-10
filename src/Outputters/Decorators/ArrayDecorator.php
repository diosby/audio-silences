<?php

namespace SegmentGenerator\Outputters\Decorators;

use SegmentGenerator\Outputters\Contracts\ArrayOutputterGetter;

/**
 * Combines `OutputGetter::getOutput()` with
 * `StringOutputterGetter::getOutput(): ?array` to return an array.
 */
abstract class ArrayDecorator extends AbstractOutputGetter implements ArrayOutputterGetter
{
    /**
     * Returns an array of the outputter.
     *
     * @return array|null
     */
    public function getOutput(): ?array
    {
        return $this->output;
    }
}
