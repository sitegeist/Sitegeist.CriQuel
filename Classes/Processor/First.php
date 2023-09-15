<?php

namespace Sitegeist\CriQuel\Processor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Sitegeist\CriQuel\ProcessorInterface;

class First implements ProcessorInterface
{
    public function apply(Nodes $nodes): Nodes
    {
        return Nodes::fromArray([$nodes->first()]);
    }
}
