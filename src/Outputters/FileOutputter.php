<?php

namespace SegmentGenerator\Outputters;

use InvalidArgumentException;
use SegmentGenerator\Contracts\Outputter;
use SegmentGenerator\Entities\SegmentCollection;
use SegmentGenerator\Outputters\Contracts\StringOutputterGetter;

/**
 * Outputs segments to a file. Works only with the `StringOutputterGetter`
 * interface.
 */
class FileOutputter implements Outputter
{
    /**
     * A filename for saving segments.
     *
     * @var string
     */
    protected $filename;

    /**
     * A string getter.
     *
     * @var StringOutputterGetter
     */
    protected $outputter;

    /**
     * Initializes an outputter to write segments into the given file
     * through a string outputter.
     *
     * @param string $filename
     * @param StringOutputterGetter $outputter
     */
    public function __construct(string $filename, StringOutputterGetter $outputter)
    {
        if (empty($filename)) {
            throw new InvalidArgumentException('The filename wasn\'t given.');
        } elseif (file_exists($filename) && !is_writable($filename)) {
            throw new InvalidArgumentException('The file cannot be rewritten.');
        } elseif (file_exists($filename) && !is_file($filename)) {
            throw new InvalidArgumentException('The file cannot be written. The path is a directory.');
        }

        $this->filename = $filename;
        $this->outputter = $outputter;
    }

    /**
     * Outputs the given segments to the given file.
     *
     * @param SegmentCollection $segments
     * @return void
     */
    public function output(SegmentCollection $segments): void
    {
        $this->outputter->output($segments);
        $this->save();
    }

    /**
     * Saves the output to the file.
     *
     * @return void
     */
    protected function save(): void
    {
        if (file_put_contents($this->filename, $this->outputter->getOutput()) === false) {
            throw new InvalidArgumentException('The file wasn\'t written.');
        }
    }
}
