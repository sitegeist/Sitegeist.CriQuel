<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel\Processor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindAncestorNodesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepository\Core\Projection\ContentGraph\NodeTypeConstraints;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Sitegeist\CriQuel\ProcessorInterface;

class WithAncestors implements ProcessorInterface
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $crRegistry;

    protected ?NodeTypeConstraints $nodeTypeConstraints = null;

    public function __construct(NodeTypeConstraints|string $nodeTypeConstraints = null)
    {
        if (is_string($nodeTypeConstraints)) {
            $this->nodeTypeConstraints = NodeTypeConstraints::fromFilterString($nodeTypeConstraints);
        } elseif ($nodeTypeConstraints instanceof NodeTypeConstraints)  {
            $this->nodeTypeConstraints = $nodeTypeConstraints;
        }
    }

    public function apply(Nodes $nodes): Nodes
    {
        $findAncestorFilter = FindAncestorNodesFilter::create(
            $this->nodeTypeConstraints
        );

        $ancestorNodesArray = [];
        foreach ($nodes as $node) {
            $subgraph = $this->crRegistry->subgraphForNode($node);
            $ancestors = $subgraph->findAncestorNodes(
                $node->nodeAggregateId,
                $findAncestorFilter
            );
            $ancestorNodesArray[] = [$node, ...iterator_to_array($ancestors)];
        }
        return Nodes::fromArray(array_merge(...$ancestorNodesArray));
    }
}
