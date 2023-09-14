<?php

namespace Sitegeist\CriQuel\Processor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Sitegeist\CriQuel\ProcessorInterface;
use Sitegeist\CriQuel\TransientOperationInterface;

class Add implements ProcessorInterface
{
    private Nodes $nodes;
    public function __construct(Nodes|Node ...$items)
    {
        $nodes = Nodes::createEmpty();
        foreach ($items as $item) {
            if ($item instanceof Node) {
                $nodes = $nodes->merge(Nodes::fromArray([$item]));
            } elseif ($item instanceof Nodes) {
                $nodes = $nodes->merge($item);
            }
        }
        $this->nodes = $nodes;
    }

    public function apply(Nodes $nodes): Nodes
    {
        return $nodes->merge($this->nodes);
    }
}