<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel\Processor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindChildNodesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindClosestNodeFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\Criteria\PropertyValueCriteriaInterface;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\PropertyValueCriteriaParser;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\NodeType\NodeTypeCriteria;
use Neos\ContentRepository\NodeAccess\FlowQueryOperations\CreateNodeHashTrait;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Sitegeist\CriQuel\ProcessorInterface;

class ClosestProcessor implements ProcessorInterface
{
    use CreateNodeHashTrait;

    #[Flow\Inject]
    protected ContentRepositoryRegistry $crRegistry;

    protected NodeTypeCriteria $nodeTypeCriteria;

    public function __construct(NodeTypeCriteria|string $nodeTypeCriteria)
    {
        if (is_string($nodeTypeCriteria)) {
            $this->nodeTypeCriteria = NodeTypeCriteria::fromFilterString($nodeTypeCriteria);
        } elseif ($nodeTypeCriteria instanceof NodeTypeCriteria) {
            $this->nodeTypeCriteria = $nodeTypeCriteria;
        }
    }

    public function process(Nodes $nodes): Nodes
    {
        $filter = FindClosestNodeFilter::create(
            nodeTypes: $this->nodeTypeCriteria,
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
