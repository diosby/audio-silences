<?php

namespace SegmentGenerator;

class Chapter implements \Countable
{
    /**
     * Segments of the chapter.
     *
     * @var ChapterPart[]
     */
    protected $parts = [];

    /**
     * A title of the chapter.
     *
     * @var string|null
     */
    protected $title;

    public function count(): int
    {
        return count($this->parts);
    }

    /**
     * Returns segments of the chapter.
     *
     * @return ChapterPart[]
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    /**
     * Sets the given title to the chapter.
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Returns a title of the chapter.
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function first(): ?ChapterPart
    {
        return $this->parts[0] ?? null;
    }

    public function last(): ?ChapterPart
    {
        return $this->parts[$this->count() - 1] ?? null;
    }

    /**
     * Adds a new part of the chapter by the given start offset.
     * Returns the new part.
     *
     * @param Interval $start
     * @return ChapterPart
     */
    public function start(Interval $start): ChapterPart
    {
        return $this->parts[] = new ChapterPart($start, 0, $this);
    }

    /**
     * Adds a new part of the chapter by the given finish interval of the last
     * part and by the given start interval of the new part.
     * Returns the new chapter part.
     *
     * @param Interval $finish
     * @param Interval $start
     * @return ChapterPart
     */
    public function plus(Interval $finish, Interval $start): ChapterPart
    {
        $this->finish($finish);

        return $this->start($start);
    }

    /**
     * Finishes the last part of the chapter by the given interval of the last
     * offset.
     *
     * @param Interval $finish
     * @return void
     */
    public function finish(Interval $finish): ChapterPart
    {
        if ($this->count()) {
            $this->last()->setDurationByInterval($finish);
        } else {
            $this->parts[] = $part = new ChapterPart(new Interval('PT0S'), 0, $this);
            $part->setDurationByInterval($finish);
        }

        return $this->last();
    }

    /**
     * Returns an offset of the first part of the chapter or the zero interval.
     *
     * @return Interval
     */
    public function getOffset(): Interval
    {
        return $this->count() ? $this->first()->getOffset() : new Interval('PT0S');
    }

    /**
     * Returns a duration of the chapter.
     *
     * @return int
     */
    public function getDuration(): int
    {
        return $this->last()->getOffset()->difference($this->getOffset()) + $this->last()->getDuration();
    }
}
