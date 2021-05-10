<?php

namespace SegmentGenerator\Outputters\Contracts;

use SegmentGenerator\Contracts\Outputter;

/**
 * Returns an output of the outputter.
 */
interface OutputGetter extends Outputter
{
    /**
     * Returns an output of the outputter.
     *
     * @return mixed|null
     */
    public function getOutput();
}
