<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel\Operations;

use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindChildNodesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Sitegeist\CriQuel\OperationInterface;

class ChildrenOperation implements OperationInterface
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
        $filter = FindChildNodesFilter::create(
            $this->nodeTypeConstraints
        );
        $results = Nodes::createEmpty();
        foreach ($nodes as $node) {
            $subgraph = $this->crRegistry->subgraphForNode($node);
            $children = $subgraph->findChildNodes(
                $node->nodeAggregateId,
                $filter
            );
            $results = $results->merge($children);
        }
        return $results;
    }
}
