<?php

namespace SegmentGenerator;

class SegmentCollection implements \Countable
{
    /**
     * Segments of the collection.
     *
     * @var Segment[]
     */
    protected $items = [];

    /**
     * Initializes a collection with segments.
     *
     * @param Segment[] $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Counts segments.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Returns segments of the collection.
     *
     * @return Segment[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function toArray(): array
    {
        return array_map(function(Segment $segment) {
            return $segment->toArray();
        }, $this->items);
    }
}
