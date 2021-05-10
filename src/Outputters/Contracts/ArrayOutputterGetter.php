<?php

namespace SegmentGenerator\Outputters\Contracts;

/**
 * Returns an array from the `getOutput()` method.
 */
interface ArrayOutputterGetter extends OutputGetter
{
    /**
     * Returns an array of the outputter.
     *
     * @return array|null
     */
    public function getOutput(): ?array;
}
