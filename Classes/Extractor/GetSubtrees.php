<?php

namespace Sitegeist\CriQuel\Extractor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindSubtreeFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepository\Core\Projection\ContentGraph\NodeTypeConstraints;
use Neos\ContentRepository\Core\Projection\ContentGraph\Subtree;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Sitegeist\CriQuel\ExtractorInterface;
use Neos\ContentRepository\Core\Projection\ContentGraph\Subtrees as CrSubtrees;

class GetSubtrees implements ExtractorInterface
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $crRegistry;

    protected ?NodeTypeConstraints $nodeTypeConstraints = null;

    public function __construct(NodeTypeConstraints|string $nodeTypeConstraints = null)
    {
        if (is_string($nodeTypeConstraints)) {
            $this->nodeTypeConstraints = NodeTypeConstraints::fromFilterString($nodeTypeConstraints);
        } elseif ($nodeTypeConstraints instanceof NodeTypeConstraints) {
            $this->nodeTypeConstraints = $nodeTypeConstraints;
        }
    }

    public function extract(Nodes $nodes): CrSubtrees
    {
        $filter = FindSubtreeFilter::create(
            $this->nodeTypeConstraints
        );
        $subtrees = [];
        foreach ($nodes as $node) {
            $subgraph = $this->crRegistry->subgraphForNode($node);
            $subtree = $subgraph->findSubtree($node->nodeAggregateId, $filter);
            if ($subtree instanceof Subtree) {
                $subtrees[] = $subtree;
            }
        }
        return CrSubtrees::fromArray($subtrees);
    }
}
