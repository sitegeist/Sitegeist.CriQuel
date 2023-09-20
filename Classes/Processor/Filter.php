<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel\Processor;

use Neos\ContentRepository\Core\Factory\ContentRepositoryId;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\Projection\ContentGraph\NodeTypeConstraints;
use Neos\ContentRepository\Core\Projection\ContentGraph\NodeTypeConstraintsWithSubNodeTypes;
use Neos\ContentRepository\NodeAccess\FlowQueryOperations\CreateNodeHashTrait;
use Neos\Flow\Annotations as Flow;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Sitegeist\CriQuel\ProcessorInterface;
use Sitegeist\CriQuel\Trait\MatchesNodeTypeConstraintTrait;

class Filter implements ProcessorInterface
{
    use MatchesNodeTypeConstraintTrait;

    #[Flow\Inject]
    protected ContentRepositoryRegistry $crRegistry;

    protected ?NodeTypeConstraints $nodeTypeConstraints = null;

    public function __construct(NodeTypeConstraints|string $nodeTypeConstraints = null)
    {
        if (is_string($nodeTypeConstraints)) {
            $this->nodeTypeConstraints = NodeTypeConstraints::fromFilterString($nodeTypeConstraints);
        } elseif ($nodeTypeConstraints instanceof NodeTypeConstraints)  {
            $this->nodeTypeConstraints = $nodeTypeConstraints;
        }
    }

    public function apply(Nodes $nodes): Nodes
    {
        $filteredNodes = [];
        if ($first = $nodes->first()) {
            $nodeTypeManager = $this->crRegistry->get(ContentRepositoryId::fromString('default'))->getNodeTypeManager();
            $constraints = NodeTypeConstraintsWithSubNodeTypes::create($this->nodeTypeConstraints, $nodeTypeManager);
            foreach ($nodes as $node) {
                if ($this->nodeTypeConstraints === null || $this->matchesNodeTypeConstraint($node, $constraints)) {
                    $filteredNodes[] = $node;
                }
            }
        }
        return Nodes::fromArray($filteredNodes);
    }
}
