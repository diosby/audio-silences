<?php

namespace SegmentGenerator;

use SegmentGenerator\Contracts\DebugMode;

/**
 * Segments chapters.
 */
class ChapterSegmentator implements DebugMode
{
    use DebugLog;

    protected $maxDurationOfSegment;

    private $segments = [];

    public function __construct(int $maxDurationOfSegment = null)
    {
        $this->maxDurationOfSegment = $maxDurationOfSegment;
    }

    /**
     * Segments the given chapters.
     *
     * @param ChapterCollection $chapters
     * @return SegmentCollection
     */
    public function segment(ChapterCollection $chapters): SegmentCollection
    {
        $this->segments = [];

        foreach ($chapters->getItems() as $key => $chapter) {
            if (empty($this->maxDurationOfSegment) || $this->isFull($chapter)) {
                $this->fullChapter($chapter, $key + 1);
            } else {
                $this->multipleChapter($chapter, $key + 1);
            }
        }

        return new SegmentCollection($this->segments);
    }

    /**
     * Checks whethet the given chapter is full.
     *
     * @param Chapter $chapter
     * @return bool
     */
    public function isFull(Chapter $chapter): bool
    {
        return $this->maxDurationOfSegment >= $chapter->getDuration() || $chapter->count() === 1;
    }

    /**
     * Adds the given full chapter.
     *
     * @param Chapter $chapter
     * @param int $index
     * @return void
     */
    protected function fullChapter(Chapter $chapter, int $index): void
    {
        $this->log("%d. A full chapter.\n", $index);
        $this->segments[] = new Segment($chapter->getOffset(), $chapter->getTitle());
    }

    /**
     * Adds the given multiple chapter.
     *
     * @param Chapter $chapter
     * @param int $index
     * @return void
     */
    protected function multipleChapter(Chapter $chapter, int $index): void
    {
        $this->log("%d. A multiple chapter.\n", $index);
        $numberOfPart = 0;
        $duration = 0;

        foreach ($chapter->getParts() as $key => $part) {
            $this->log("%d.%d. A part of segments: %dms.\n", $index, $key + 1, $part->getDuration());

            if ($this->maxDurationOfSegment <= $part->getDuration()) {
                // It is a big segment.
                $this->log("[L] The part is long.\n");
                $this->partialSegment($part, ++$numberOfPart);
                $duration = 0;
            } elseif ($duration === 0) {
                // It is a start segment of the multiple segments.
                $this->log("[F] A new start part of multiple segments.\n");
                $this->partialSegment($part, ++$numberOfPart);
                $duration += $part->getDuration();
            } elseif ($this->maxDurationOfSegment <= $duration + $part->getDuration()) {
                // The part overloads the duration.
                $this->log("[O] The duration is oversize: %d.\n", $duration + $part->getDuration());
                $this->partialSegment($part, ++$numberOfPart);
                $duration = 0;
            } elseif ($part->getDuration() === 0) {
                // The last empty segment that has an empty duration.
                $this->log("[E] The last part of the chapter. It is empty.\n");
                $this->partialSegment($part, ++$numberOfPart);
            } else {
                $this->log("[N] To the next segment. Add the duration.\n");
                $duration += $part->getDuration();
            }
        }
    }

    /**
     * Adds the given partial segment.
     *
     * @param ChapterPart $part
     * @param int $index
     * @return void
     */
    protected function partialSegment(ChapterPart $part, int $index): void
    {
        $this->segments[] = $lastSegment = new Segment($part->getOffset(), $part->getParent()->getTitle());
        $title = $lastSegment->getTitle() ? $lastSegment->getTitle() . ", part $index" : "Part $index";
        $lastSegment->setTitle($title);
    }
}
