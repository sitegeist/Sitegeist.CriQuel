<?php

namespace Sitegeist\CriQuel\Processor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Sitegeist\CriQuel\ProcessorInterface;

class FirstProcessor implements ProcessorInterface
{
    public function process(Nodes $nodes): Nodes
    {
        $node = $nodes->first();
        if ($node instanceof Node) {
            return Nodes::fromArray([$node]);
        }
        return Nodes::createEmpty();
    }
}
