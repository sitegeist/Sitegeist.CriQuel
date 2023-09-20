<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel\Processor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindChildNodesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepository\Core\Projection\ContentGraph\NodeTypeConstraints;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Sitegeist\CriQuel\ProcessorInterface;

class Children implements ProcessorInterface
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
