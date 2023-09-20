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
        } elseif ($nodeTypeConstraints instanceof PropertyValueCriteriaInterface) {
            $this->propertyValueCriteria = $propertyValueCriteria;
        }
    }

    public function apply(Nodes $nodes): Nodes
    {
        $filteredNodes = [];
        if ($first = $nodes->first()) {
            $nodeTypeManager = $this->crRegistry->get(ContentRepositoryId::fromString('default'))->getNodeTypeManager();
            $constraints = NodeTypeConstraintsWithSubNodeTypes::create($this->nodeTypeConstraints, $nodeTypeManager);
            foreach ($nodes as $node) {
                if (
                    ($this->nodeTypeConstraints === null || NodeCriteriaMatcher::matchesNodeTypeConstraint($node, $constraints))
                    && ($this->propertyValueCriteria === null || NodeCriteriaMatcher::matchesPropertyConstraint($node, $this->propertyValueCriteria))
                ) {
                    $filteredNodes[] = $node;
                }
            }
        }
        return Nodes::fromArray($filteredNodes);
    }
}
