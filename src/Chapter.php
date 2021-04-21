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
     * Returns inner silences of the chapter.
     *
     * @return Silence[]
     */
    public function getInnerSilences(): array
    {
        $silences = [];

        for ($i = 1; $i < $this->count() - 1; $i++) {
            $silences[] = $this->parts[$i]->getSilenceAfter();
        }

        return array_values(array_filter($silences));
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

    /**
     * Checks whether the given chapter is the first.
     *
     * @param ChapterPart $part
     * @return bool
     */
    public function isFirst(ChapterPart $part): bool
    {
        return $this->first() ? $this->first() === $part : false;
    }

    /**
     * Checks whether the given chapter is the last.
     *
     * @param ChapterPart $part
     * @return bool
     */
    public function isLast(ChapterPart $part): bool
    {
        return $this->last() ? $this->last() === $part : false;
    }

    /**
     * Returns the first part of the chapter.
     *
     * @return ChapterPart|null
     */
    public function first(): ?ChapterPart
    {
        return $this->parts[0] ?? null;
    }

    /**
     * Returns the last part of the chapter.
     *
     * @return ChapterPart|null
     */
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
     * Add a new part of the chapter by the given silence before the part.
     * Sets the silence before the part.
     * Returns the new part.
     *
     * @param Silence $silence
     * @return ChapterPart
     */
    public function startBySilence(Silence $silence): ChapterPart
    {
        return $this->start($silence->getUntil())->setSilenceBefore($silence);
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
     * Adds a new part of the chapter by the given silence between parts.
     * Sets the silence after the part.
     * Returns the new chapter part.
     *
     * @param Silence $silence
     * @return ChapterPart
     */
    public function plusBySilence(Silence $silence): ChapterPart
    {
        $this->finish($silence->getFrom())->setSilenceAfter($silence);

        return $this->start($silence->getUntil())->setSilenceBefore($silence);
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
     * Finishes the last part of the chapter by the given silence after the part.
     * Sets the silence after the part.
     * Returns the new part.
     *
     * @param Silence $silence
     * @return ChapterPart
     */
    public function finishBySilence(Silence $silence): ChapterPart
    {
        return $this->finish($silence->getFrom())->setSilenceAfter($silence);
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
