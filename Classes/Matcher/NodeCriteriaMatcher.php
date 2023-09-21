<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel\Matcher;

use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\Criteria\AndCriteria;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\Criteria\NegateCriteria;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\Criteria\OrCriteria;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\Criteria\PropertyValueContains;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\Criteria\PropertyValueCriteriaInterface;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\Criteria\PropertyValueEndsWith;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\Criteria\PropertyValueEquals;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\Criteria\PropertyValueGreaterThan;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\Criteria\PropertyValueGreaterThanOrEqual;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\Criteria\PropertyValueLessThan;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\Criteria\PropertyValueLessThanOrEqual;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\Criteria\PropertyValueStartsWith;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\Projection\ContentGraph\NodeTypeConstraints;
use Neos\ContentRepository\Core\Projection\ContentGraph\NodeTypeConstraintsWithSubNodeTypes;

final class NodeCriteriaMatcher
{
    public static function matchesNodeTypeConstraint(Node $node, NodeTypeConstraints|NodeTypeConstraintsWithSubNodeTypes $nodeTypeConstraints): bool
    {
        $nodeTypeName = $node->nodeTypeName;
        $allowed = false;
        $forbidden = false;
        foreach ($nodeTypeConstraints->explicitlyAllowedNodeTypeNames as $allowedNodeTypeName) {
            if ($nodeTypeName->equals($allowedNodeTypeName)) {
                $allowed = true;
                break;
            }
        }
        foreach ($nodeTypeConstraints->explicitlyDisallowedNodeTypeNames as $disallowedNodeTypeName) {
            if ($nodeTypeName->equals($disallowedNodeTypeName)) {
                $forbidden = true;
                break;
            }
        }
        return $allowed && !$forbidden;
    }

    public static function matchesPropertyConstraint(Node $node, PropertyValueCriteriaInterface $propertyValueCriteria): bool
    {
        return match (get_class($propertyValueCriteria)) {
            AndCriteria::class => self::matchesPropertyConstraint($node, $propertyValueCriteria->criteria1) && self::matchesPropertyConstraint($node, $propertyValueCriteria->criteria2),
            OrCriteria::class=> self::matchesPropertyConstraint($node, $propertyValueCriteria->criteria1) || self::matchesPropertyConstraint($node, $propertyValueCriteria->criteria2),
            NegateCriteria::class => !self::matchesPropertyConstraint($node, $propertyValueCriteria->criteria),
            PropertyValueContains::class => str_contains($node->getProperty($propertyValueCriteria->propertyName->value), $propertyValueCriteria->value),
            PropertyValueEndsWith::class => str_ends_with($node->getProperty($propertyValueCriteria->propertyName->value), $propertyValueCriteria->value),
            PropertyValueEquals::class => $node->getProperty($propertyValueCriteria->propertyName->value) == $propertyValueCriteria->value,
            PropertyValueGreaterThan::class => $node->getProperty($propertyValueCriteria->propertyName->value) > $propertyValueCriteria->value,
            PropertyValueGreaterThanOrEqual::class => $node->getProperty($propertyValueCriteria->propertyName->value) >= $propertyValueCriteria->value,
            PropertyValueLessThan::class => $node->getProperty($propertyValueCriteria->propertyName->value) < $propertyValueCriteria->value,
            PropertyValueLessThanOrEqual::class => $node->getProperty($propertyValueCriteria->propertyName->value) <= $propertyValueCriteria->value,
            PropertyValueStartsWith::class => str_starts_with($node->getProperty($propertyValueCriteria->propertyName->value), $propertyValueCriteria->value),
            default => (throw new \InvalidArgumentException('unknown criteria of type ' . get_class($propertyValueCriteria)))
        };
    }
}
