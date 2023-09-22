<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel\Processor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindChildNodesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepository\Core\SharedModel\Node\NodeName;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Sitegeist\CriQuel\ProcessorInterface;

class Tethered implements ProcessorInterface
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $crRegistry;

    protected NodeName $name;

    public function __construct(NodeName|string $name)
    {
        if (is_string($name)) {
            $this->name = NodeName::fromString($name);
        } else {
            $this->name = $name;
        }
    }

    public function process(Nodes $nodes): Nodes
    {
        $nodeArray = [];
        foreach ($nodes as $node) {
            $subgraph = $this->crRegistry->subgraphForNode($node);
            $tetheredNode = $subgraph->findChildNodeConnectedThroughEdgeName($node->nodeAggregateId, $this->name);
            if ($tetheredNode instanceof Node) {
                $nodeArray[] = $tetheredNode;
            }
        }
        return Nodes::fromArray($nodeArray);
    }
}
