<?php

namespace SegmentGenerator\Outputters\Contracts;
/**
 * Returns a string from the `getOutput()` method.
 */
interface StringOutputterGetter extends OutputGetter
{
    /**
     * Returns a string of the outputter.
     *
     * @return string|null
     */
    public function getOutput(): ?string;
}
