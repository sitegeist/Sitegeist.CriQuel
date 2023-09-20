<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel\Trait;

use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\Projection\ContentGraph\NodeTypeConstraints;
use Neos\ContentRepository\Core\Projection\ContentGraph\NodeTypeConstraintsWithSubNodeTypes;

trait MatchesNodeTypeConstraintTrait
{
    protected function matchesNodeTypeConstraint(Node $node, NodeTypeConstraints|NodeTypeConstraintsWithSubNodeTypes $nodeTypeConstraints): bool
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
}
