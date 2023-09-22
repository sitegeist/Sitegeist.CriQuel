<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel\Processor;

use Neos\ContentRepository\Core\Factory\ContentRepositoryId;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\Criteria\PropertyValueCriteriaInterface;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\PropertyValueCriteriaParser;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\Projection\ContentGraph\NodeTypeConstraints;
use Neos\ContentRepository\Core\Projection\ContentGraph\NodeTypeConstraintsWithSubNodeTypes;
use Neos\ContentRepository\NodeAccess\FlowQueryOperations\CreateNodeHashTrait;
use Neos\Flow\Annotations as Flow;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Sitegeist\CriQuel\ProcessorInterface;
use Sitegeist\CriQuel\Matcher\NodeCriteriaMatcher;

class Filter implements ProcessorInterface
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
        $filteredNodes = [];
        /** @todo adjust after node type manager is created elsewhere */
        $nodeTypeManager = $this->crRegistry->get(ContentRepositoryId::fromString('default'))->getNodeTypeManager();
        $nodeTypeConstraintsWithSubNodeTypes = ($this->nodeTypeConstraints instanceof NodeTypeConstraints) ? NodeTypeConstraintsWithSubNodeTypes::create($this->nodeTypeConstraints, $nodeTypeManager) : null;
        foreach ($nodes as $node) {
            if (
                ($nodeTypeConstraintsWithSubNodeTypes === null || $nodeTypeConstraintsWithSubNodeTypes->matches($node->nodeTypeName))
                && ($this->propertyValueCriteria === null || NodeCriteriaMatcher::matchesPropertyConstraint($node, $this->propertyValueCriteria))
            ) {
                $filteredNodes[] = $node;
            }
        }
        return Nodes::fromArray($filteredNodes);
    }
}
