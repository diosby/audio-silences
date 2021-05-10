<?php

namespace SegmentGenerator\Contracts;

/**
 * The abtract application with a facade of the segment generation.
 */
abstract class Application
{
    /**
     * Runs a process of silences to output segments.
     *
     * @return void
     */
    public function run(): void
    {
        $silences = $this->getFacade()->getSilences();
        $segments = $this->getFacade()->getSegmentator()->segment($silences);
        $this->getFacade()->getOutputter()->output($segments);
    }

    /**
     * Returns a segment generator.
     *
     * @return SegmentGeneratorFacade
     */
    abstract public function getFacade(): SegmentGeneratorFacade;
}
