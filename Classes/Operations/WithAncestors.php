<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel\Operations;

use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindAncestorNodesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepository\Core\Projection\ContentGraph\NodeTypeConstraints;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Sitegeist\CriQuel\OperationInterface;

class WithAncestors implements OperationInterface
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $crRegistry;

    protected ?string $nodeTypeConstraints;

    public function __construct(string $nodeTypeConstraints = null)
    {
        $this->nodeTypeConstraints = $nodeTypeConstraints;
    }

    public function apply(Nodes $nodes): Nodes
    {
        $findAncestorFilter = FindAncestorNodesFilter::create(
            NodeTypeConstraints::fromFilterString($this->nodeTypeConstraints)
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
