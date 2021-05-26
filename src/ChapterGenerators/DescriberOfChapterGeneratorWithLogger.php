<?php

namespace SegmentGenerator\ChapterGenerators;

use SegmentGenerator\Contracts\Logger;
use SegmentGenerator\Entities\Silence;

/**
 * The describer of a chapter generator.
 */
class DescriberOfChapterGeneratorWithLogger extends DescriberOfChapterGenerator
{
    /**
     * A logger.
     *
     * @var Logger
     */
    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Returns a logger.
     *
     * @return Logger
     */
    public function getLogger(): Logger
    {
        return $this->logger;
    }

    /**
     * Describes the given silence.
     *
     * @param Silence $silence
     * @return DescriberOfChapterGenerator
     */
    public function describeSilence(Silence $silence): DescriberOfChapterGenerator
    {
        $this->logger->log(
            "Silence #%s: %d ms, from %s until %s.\n",
            $this->getSilenceIndex(),
            $silence->getDuration(),
            (string) $silence->getFrom(),
            (string) $silence->getUntil()
        );

        return $this;
    }

    /**
     * Describes the given transition.
     *
     * @param Silence $silence
     * @return DescriberOfChapterGenerator
     */
    public function describeTransition(Silence $silence): DescriberOfChapterGenerator
    {
        $this->logger->log(
            "Silence #%s is the transition: %d ms.\n",
            $this->getSilenceIndex(),
            $silence->getDuration()
        );

        return $this;
    }

    /**
     * Describes the given pause.
     *
     * @param Silence $silence
     * @return DescriberOfChapterGenerator
     */
    public function describePause(Silence $silence): DescriberOfChapterGenerator
    {
        $this->logger->log(
            "Silence #%s is the pause: %d ms.\n",
            $this->getSilenceIndex(),
            $silence->getDuration()
        );

        return $this;
    }
}
