<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel\Processor;

use Neos\ContentRepository\Core\NodeType\ConstraintCheck;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\Criteria\PropertyValueCriteriaInterface;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\PropertyValueCriteriaParser;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\NodeType\NodeTypeCriteria;
use Neos\Flow\Annotations as Flow;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Sitegeist\CriQuel\ProcessorInterface;
use Sitegeist\CriQuel\Matcher\NodeCriteriaMatcher;

class FilterProcessor implements ProcessorInterface
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $crRegistry;

    protected ?NodeTypeCriteria $nodeTypeCriteria = null;
    protected ?PropertyValueCriteriaInterface $propertyValueCriteria = null;

    public function __construct(NodeTypeCriteria|string $nodeTypeCriteria = null, PropertyValueCriteriaInterface|string $propertyValueCriteria = null)
    {
        if (is_string($nodeTypeCriteria)) {
            $this->nodeTypeCriteria = NodeTypeCriteria::fromFilterString($nodeTypeCriteria);
        } elseif ($nodeTypeCriteria instanceof NodeTypeCriteria) {
            $this->nodeTypeCriteria = $nodeTypeCriteria;
        }

        if (is_string($propertyValueCriteria)) {
            $this->propertyValueCriteria = PropertyValueCriteriaParser::parse($propertyValueCriteria);
        } elseif ($propertyValueCriteria instanceof PropertyValueCriteriaInterface) {
            $this->propertyValueCriteria = $propertyValueCriteria;
        }
    }

    public function process(Nodes $nodes): Nodes
    {
        $filteredNodes = [];
        $nodeTypeConstraintCheck = $this->nodeTypeCriteria ? ConstraintCheck::create($this->nodeTypeCriteria) : null;
        foreach ($nodes as $node) {
            if (
                ($nodeTypeConstraintCheck === null || $nodeTypeConstraintCheck->isNodeTypeAllowed($node->nodeTypeName))
                && ($this->propertyValueCriteria === null || NodeCriteriaMatcher::matchesPropertyConstraint($node, $this->propertyValueCriteria))
            ) {
                $filteredNodes[] = $node;
            }
        }
        return Nodes::fromArray($filteredNodes);
    }
}
