<?php

namespace SegmentGenerator\Outputters\Decorators;

use SegmentGenerator\Entities\SegmentCollection;

/**
 * Places an output of the inner outputter to the array with 'segments'.
 */
class ArrayWrapperDecorator extends ArrayDecorator
{
    /**
     * Outputs the given segments through the inner outputter and wraps them
     * to the array with 'segments'.
     *
     * @param SegmentCollection $segments
     * @return void
     */
    public function output(SegmentCollection $segments): void
    {
        $this->outputter->output($segments);
        $this->output = $this->wrap();
    }

    /**
     * Wraps the output of the last output.
     *
     * @return array
     */
    protected function wrap(): array
    {
        return ['segments' => $this->outputter->getOutput()];
    }
}
