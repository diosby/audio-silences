<?php

namespace SegmentGenerator\App;

use SegmentGenerator\Contracts\Application as Base;
use SegmentGenerator\Contracts\SegmentGeneratorFacade;

class Application extends Base
{
    /**
     * A segment generator facade.
     *
     * @var SegmentGeneratorFacade
     */
    private $facade;

    public function __construct(SegmentGeneratorFacade $facade)
    {
        $this->facade = $facade;
    }

    public function getFacade(): SegmentGeneratorFacade
    {
        return $this->facade;
    }
}
