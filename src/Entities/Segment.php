<?php

namespace SegmentGenerator\Entities;

class Segment
{
    /**
     * An offset of the segment.
     *
     * @var Interval
     */
    protected $offset;

    /**
     * A title of the segment.
     *
     * @param string $title
     */
    protected $title;

    public function __construct(Interval $offset, string $title = null)
    {
        $this->offset = $offset;
        $this->title = $title;
    }

    public function getOffset(): Interval
    {
        return $this->offset;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function toArray(): array
    {
        $array['offset'] = (string) $this->offset;

        if ($this->title) {
            $array['title'] = $this->title;
        }

        return $array;
    }
}
