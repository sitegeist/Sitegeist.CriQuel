<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel\Processor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindReferencesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\Criteria\PropertyValueCriteriaInterface;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\PropertyValueCriteriaParser;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\NodeType\NodeTypeCriteria;
use Neos\ContentRepository\Core\SharedModel\Node\ReferenceName;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Sitegeist\CriQuel\ProcessorInterface;

class ReferencingNodesProcessor implements ProcessorInterface
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $contentRepositoryRegistry;

    protected ?ReferenceName $referenceName = null;
    protected ?NodeTypeCriteria $nodeTypeCriteria = null;
    protected ?PropertyValueCriteriaInterface $nodePropertyValueCriteria = null;
    protected ?PropertyValueCriteriaInterface $referencePropertyValueCriteria = null;

    public function __construct(string|ReferenceName $referenceName = null, string|NodeTypeCriteria $nodeTypeCriteria = null, string|PropertyValueCriteriaInterface $nodePropertyValueCriteria = null, string|PropertyValueCriteriaInterface $referencePropertyValueCriteria = null)
    {
        if (is_string($referenceName)) {
            $this->referenceName = ReferenceName::fromString($referenceName);
        } elseif ($referenceName instanceof ReferenceName) {
            $this->referenceName = $referenceName;
        }
        if (is_string($nodeTypeCriteria)) {
            $this->nodeTypeCriteria = NodeTypeCriteria::fromFilterString($nodeTypeCriteria);
        } elseif ($nodeTypeCriteria instanceof NodeTypeCriteria) {
            $this->nodeTypeCriteria = $nodeTypeCriteria;
        }
        if (is_string($nodePropertyValueCriteria)) {
            $this->nodePropertyValueCriteria = PropertyValueCriteriaParser::parse($nodePropertyValueCriteria);
        } elseif ($nodePropertyValueCriteria instanceof PropertyValueCriteriaInterface) {
            $this->nodePropertyValueCriteria = $nodePropertyValueCriteria;
        }
        if (is_string($referencePropertyValueCriteria)) {
            $this->referencePropertyValueCriteria = PropertyValueCriteriaParser::parse($referencePropertyValueCriteria);
        } elseif ($referencePropertyValueCriteria instanceof PropertyValueCriteriaInterface) {
            $this->referencePropertyValueCriteria = $referencePropertyValueCriteria;
        }
    }

    public function process(Nodes $nodes): Nodes
    {
        $findReferencesFilter = FindReferencesFilter::create(
            referenceName: $this->referenceName,
            nodeTypes: $this->nodeTypeCriteria,
            nodePropertyValue: $this->nodePropertyValueCriteria,
            referencePropertyValue: $this->nodePropertyValueCriteria
        );
        $result = Nodes::createEmpty();
        foreach ($nodes as $node) {
            $subgraph = $this->contentRepositoryRegistry->subgraphForNode($node);
            $references = $subgraph->findReferences($node->nodeAggregateId, $findReferencesFilter);
            $result = $result->merge($references->getNodes());
        }
        return $result;
    }
}
