<?php

namespace SegmentGenerator\Outputters\Decorators;

use SegmentGenerator\Entities\SegmentCollection;

/**
 * Wraps an output of the inner outputter to JSON.
 */
class JsonDecorator extends StringDecorator
{
    /**
     * Outputs the given segments through the inner outputter and converts them
     * to JSON.
     *
     * @param SegmentCollection $segments
     * @return void
     */
    public function output(SegmentCollection $segments): void
    {
        $this->outputter->output($segments);
        $this->output = $this->convert();
    }

    /**
     * Converts an output of the outputter segmentation to JSON.
     *
     * @return string
     */
    protected function convert(): string
    {
        return json_encode($this->outputter->getOutput(), JSON_PRETTY_PRINT);
    }
}
