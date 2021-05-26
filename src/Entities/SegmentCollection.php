<?php

namespace SegmentGenerator\Entities;

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
     * Adds the given interval and given title.
     *
     * @param Interval $interval
     * @param string $title
     * @return Segment
     */
    public function add(Interval $interval, string $title = null): Segment
    {
        return $this->items[] = new Segment($interval, $title);
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
