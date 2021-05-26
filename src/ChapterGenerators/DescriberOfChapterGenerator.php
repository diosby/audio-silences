<?php

namespace SegmentGenerator\ChapterGenerators;

use SegmentGenerator\Entities\Silence;

/**
 * The abstract describer of a chapter generator.
 */
abstract class DescriberOfChapterGenerator
{
    /**
     * An index of the current silence.
     *
     * @var int|string
     */
    private $index = 0;

    /**
     * Sets the given silence index.
     *
     * @param int|string $index
     * @return self
     */
    public function setSilenceIndex($index): self
    {
        $this->index = $index;

        return $this;
    }

    /**
     * Returns the current silence index.
     *
     * @return int|string
     */
    public function getSilenceIndex()
    {
        return $this->index;
    }

    /**
     * Describes the given silence.
     *
     * @param Silence $silence
     * @param int|string $silenceIndex
     * @return self
     */
    abstract public function describeSilence(Silence $silence): self;

    /**
     * Describes the given transition.
     *
     * @param Silence $silence
     * @return self
     */
    abstract public function describeTransition(Silence $silence): self;

    /**
     * Describes the given pause.
     *
     * @param Silence $silence
     * @return self
     */
    abstract public function describePause(Silence $silence): self;
}
