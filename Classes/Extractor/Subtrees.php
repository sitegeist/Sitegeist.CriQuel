<?php

namespace Sitegeist\CriQuel\Extractor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindSubtreeFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepository\Core\Projection\ContentGraph\NodeTypeConstraints;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Sitegeist\CriQuel\ExtractorInterface;
use Neos\ContentRepository\Core\Projection\ContentGraph\Subtrees as CrSubtrees;

class Subtrees implements ExtractorInterface
{
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

    public function apply(Nodes $nodes): CrSubtrees
    {
        $filter = FindSubtreeFilter::create(
            $this->nodeTypeConstraints
        );
        $subtrees = [];
        foreach ($nodes as $node) {
            $subgraph = $this->crRegistry->subgraphForNode($node);
            if ($subtree = $subgraph->findSubtree($node->nodeAggregateId, $filter)) {
                $subtrees[] = $subtree;
            }
        }
        return CrSubtrees::fromArray($subtrees);
    }
}
