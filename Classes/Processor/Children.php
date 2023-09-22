<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel\Processor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindChildNodesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\Criteria\PropertyValueCriteriaInterface;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\PropertyValueCriteriaParser;
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
    protected ?PropertyValueCriteriaInterface $propertyValueCriteria = null;

    public function __construct(NodeTypeConstraints|string $nodeTypeConstraints = null, PropertyValueCriteriaInterface|string $propertyValueCriteria = null)
    {
        if (is_string($nodeTypeConstraints)) {
            $this->nodeTypeConstraints = NodeTypeConstraints::fromFilterString($nodeTypeConstraints);
        } elseif ($nodeTypeConstraints instanceof NodeTypeConstraints) {
            $this->nodeTypeConstraints = $nodeTypeConstraints;
        }

        if (is_string($propertyValueCriteria)) {
            $this->propertyValueCriteria = PropertyValueCriteriaParser::parse($propertyValueCriteria);
        } elseif ($propertyValueCriteria instanceof PropertyValueCriteriaInterface) {
            $this->propertyValueCriteria = $propertyValueCriteria;
        }
    }

    public function process(Nodes $nodes): Nodes
    {
        $filter = FindChildNodesFilter::create(
            nodeTypeConstraints: $this->nodeTypeConstraints,
            propertyValue: $this->propertyValueCriteria
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
