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
    public static function matchesPropertyConstraint(Node $node, PropertyValueCriteriaInterface $propertyValueCriteria): bool
    {
        switch (true) {
            case $propertyValueCriteria instanceof AndCriteria:
                return self::matchesPropertyConstraint($node, $propertyValueCriteria->criteria1) && self::matchesPropertyConstraint($node, $propertyValueCriteria->criteria2);
            case $propertyValueCriteria instanceof OrCriteria:
                return self::matchesPropertyConstraint($node, $propertyValueCriteria->criteria1) || self::matchesPropertyConstraint($node, $propertyValueCriteria->criteria2);
            case $propertyValueCriteria instanceof NegateCriteria:
                return !self::matchesPropertyConstraint($node, $propertyValueCriteria->criteria);
            case $propertyValueCriteria instanceof PropertyValueContains:
                $propertyValue = $node->getProperty($propertyValueCriteria->propertyName->value);
                return (is_string($propertyValue) || $propertyValue instanceof \Stringable) ? str_contains((string)$propertyValue, $propertyValueCriteria->value) : false;
            case $propertyValueCriteria instanceof PropertyValueEndsWith:
                $propertyValue = $node->getProperty($propertyValueCriteria->propertyName->value);
                return (is_string($propertyValue) || $propertyValue instanceof \Stringable) ? str_ends_with((string)$propertyValue, $propertyValueCriteria->value) : false;
            case $propertyValueCriteria instanceof PropertyValueStartsWith:
                $propertyValue = $node->getProperty($propertyValueCriteria->propertyName->value);
                return (is_string($propertyValue) || $propertyValue instanceof \Stringable) ? str_starts_with((string)$propertyValue, $propertyValueCriteria->value) : false;
            case $propertyValueCriteria instanceof PropertyValueEquals:
                return $node->getProperty($propertyValueCriteria->propertyName->value) == $propertyValueCriteria->value;
            case $propertyValueCriteria instanceof PropertyValueGreaterThan:
                return $node->getProperty($propertyValueCriteria->propertyName->value) > $propertyValueCriteria->value;
            case $propertyValueCriteria instanceof PropertyValueGreaterThanOrEqual:
                return $node->getProperty($propertyValueCriteria->propertyName->value) >= $propertyValueCriteria->value;
            case $propertyValueCriteria instanceof PropertyValueLessThan:
                return $node->getProperty($propertyValueCriteria->propertyName->value) < $propertyValueCriteria->value;
            case $propertyValueCriteria instanceof PropertyValueLessThanOrEqual:
                return $node->getProperty($propertyValueCriteria->propertyName->value) <= $propertyValueCriteria->value;
            default:
                throw new \InvalidArgumentException('unknown criteria of type ' . get_debug_type($propertyValueCriteria));
        }
    }
}
