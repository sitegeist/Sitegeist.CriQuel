<?php

namespace Sitegeist\CriQuel\Processor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\NodeAccess\FlowQueryOperations\CreateNodeHashTrait;
use Sitegeist\CriQuel\ProcessorInterface;
use Sitegeist\CriQuel\TransientOperationInterface;

class Unique implements ProcessorInterface
{
    use CreateNodeHashTrait;

    public function __construct()
    {
    }

    public function apply(Nodes $nodes): Nodes
    {
        $nodesByHash = [];
        /** @var Node $contextNode */
        foreach ($nodes as $node) {
            $hash = $this->createNodeHash($node);
            if (!array_key_exists($hash, $nodesByHash)) {
                $nodesByHash[$hash] = $node;
            }
        }
        return Nodes::fromArray(array_values($nodesByHash));
    }
}
