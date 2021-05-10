<?php

namespace SegmentGenerator\Outputters\Decorators;

use SegmentGenerator\Outputters\Contracts\StringOutputterGetter;

/**
 * Combines `OutputGetter::getOutput()` with
 * `StringOutputterGetter::getOutput(): ?string` to return a string.
 */
abstract class StringDecorator extends AbstractOutputGetter implements StringOutputterGetter
{
    /**
     * Returns a string of the output.
     *
     * @return string|null
     */
    public function getOutput(): ?string
    {
        return $this->output;
    }
}
