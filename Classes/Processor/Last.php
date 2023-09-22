<?php

namespace Sitegeist\CriQuel\Processor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Sitegeist\CriQuel\ProcessorInterface;

class Last implements ProcessorInterface
{
    public function process(Nodes $nodes): Nodes
    {
        $array = iterator_to_array($nodes);
        $index = array_key_last($array);
        return Nodes::fromArray([$array[$index]]);
    }
}
