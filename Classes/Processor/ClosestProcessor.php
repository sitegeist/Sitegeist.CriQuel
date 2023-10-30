<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel\Processor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindChildNodesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindClosestNodeFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\Criteria\PropertyValueCriteriaInterface;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\PropertyValueCriteriaParser;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepository\Core\Projection\ContentGraph\NodeTypeConstraints;
use Neos\ContentRepository\NodeAccess\FlowQueryOperations\CreateNodeHashTrait;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Sitegeist\CriQuel\ProcessorInterface;

class ClosestProcessor implements ProcessorInterface
{
    use CreateNodeHashTrait;

    #[Flow\Inject]
    protected ContentRepositoryRegistry $crRegistry;

    protected NodeTypeConstraints $nodeTypeConstraints;

    public function __construct(NodeTypeConstraints|string $nodeTypeConstraints)
    {
        if (is_string($nodeTypeConstraints)) {
            $this->nodeTypeConstraints = NodeTypeConstraints::fromFilterString($nodeTypeConstraints);
        } elseif ($nodeTypeConstraints instanceof NodeTypeConstraints) {
            $this->nodeTypeConstraints = $nodeTypeConstraints;
        }
    }

    public function process(Nodes $nodes): Nodes
    {
        $filter = FindClosestNodeFilter::create(
            nodeTypeConstraints: $this->nodeTypeConstraints,
        );

        $results = [];
        foreach ($nodes as $node) {
            $subgraph = $this->crRegistry->subgraphForNode($node);
            $closestNode = $subgraph->findClosestNode(
                $node->nodeAggregateId,
                $filter
            );
            if ($closestNode instanceof Node) {
                $results[$this->createNodeHash($closestNode)] = $closestNode;
            }
        }
        return Nodes::fromArray(array_values($results));
    }
}
