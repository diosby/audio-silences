<?php

namespace SegmentGenerator\Contracts;

/**
 * Returns settings for a segment generator.
 */
interface GeneratorSettings
{
    /**
     * Returns a path to a source file.
     *
     * @return string
     */
    public function getSource(): string;

    /**
     * Returns a chapter transition. It is a silence duration which reliably
     * indicates a chapter transition.
     *
     * @return int|null
     */
    public function getTransition(): ?int;

    /**
     * Returns a minimal silence between parts (segments) in a chapter which
     * can be used to split a long chapter.
     *
     * @return int|null
     */
    public function getMinSilence(): ?int;

    /**
     * A duration of a segment in multiple segments after which the chapter
     * will be broken up.
     *
     * @return int|null
     */
    public function getMaxSegment(): ?int;

    /**
     * An output file with the result.
     *
     * @return string|null
     */
    public function getOutput(): ?string;

    /**
     * A debug mode.
     *
     * @return bool
     */
    public function isDebug(): bool;
}
