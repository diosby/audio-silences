<?php

namespace SegmentGenerator\Outputters\Decorators;

use SegmentGenerator\Entities\SegmentCollection;
use SegmentGenerator\Outputters\Contracts\ArrayOutputterGetter;

/**
 * An array base for an outputter. Converts segments to an array and returns
 * them through `ArrayBase::getOutput(): ?array`.
 */
class ArrayBase implements ArrayOutputterGetter
{
    /**
     * An array with segments of the last converation.
     *
     * @var array|null
     */
    protected $output;

    /**
     * Converts the segments to an array.
     *
     * @param SegmentCollection $segments
     * @return void
     */
    public function output(SegmentCollection $segments): void
    {
        $this->output = $this->convert($segments);
    }

    /**
     * Converts the given segments to an array.
     *
     * @param SegmentCollection $segments
     * @return array
     */
    protected function convert(SegmentCollection $segments): array
    {
        return $segments->toArray();
    }

    /**
     * Returns an array of the output.
     *
     * @return array
     */
    public function getOutput(): ?array
    {
        return $this->output;
    }
}
