<?php

namespace Sitegeist\CriQuel\Processor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\NodeAccess\FlowQueryOperations\CreateNodeHashTrait;
use Sitegeist\CriQuel\ProcessorInterface;
use Sitegeist\CriQuel\TransientOperationInterface;

class UniqueProcessor implements ProcessorInterface
{
    use CreateNodeHashTrait;

    public function __construct()
    {
    }

    public function process(Nodes $nodes): Nodes
    {
        $nodesByHash = [];
        foreach ($nodes as $node) {
            $hash = $this->createNodeHash($node);
            if (!array_key_exists($hash, $nodesByHash)) {
                $nodesByHash[$hash] = $node;
            }
        }
        return Nodes::fromArray(array_values($nodesByHash));
    }
}
