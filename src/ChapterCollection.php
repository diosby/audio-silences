<?php

namespace SegmentGenerator;

use SegmentGenerator\Contracts\Duration;

class ChapterCollection implements \Countable, Duration
{
    /**
     * Chapters of the collection.
     *
     * @var Chapter[]
     */
    protected $items = [];

    /**
     * Initializes a collection with chapters.
     *
     * @param Chapter[] $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Counts chapters.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Returns chapters of the collection.
     *
     * @return Chapter[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Returns a number of the chapters.
     *
     * @return int
     */
    public function getNumberOfChapters(): int
    {
        return $this->count();
    }

    /**
     * Returns a number of parts of the chapters.
     *
     * @return int
     */
    public function getNumberOfParts(): int
    {
        $segments = array_map('count', $this->items);

        return array_sum($segments);
    }

    /**
     * Returns a duration of the chapters.
     *
     * @return int
     */
    public function getDuration(): int
    {
        $durations = array_map(function (Chapter $chapter) {
            return $chapter->getDuration();
        }, $this->items);

        return array_sum($durations);
    }

    /**
     * Fills titles of the chapters.
     *
     * @param string $format A format of the title. '%d' is an index of the chapter.
     * @return void
     */
    public function fillTitles(string $format = 'Chapter %d'): void
    {
        foreach ($this->items as $key => $item) {
            $item->setTitle(sprintf($format, $key + 1));
        }
    }
}
