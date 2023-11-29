<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel\Processor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindAncestorNodesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\NodeType\NodeTypeCriteria;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Sitegeist\CriQuel\ProcessorInterface;

class WithAncestorsProcessor implements ProcessorInterface
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $crRegistry;

    protected ?NodeTypeCriteria $nodeTypeCriteria = null;

    public function __construct(NodeTypeCriteria|string $nodeTypeCriteria = null)
    {
        if (is_string($nodeTypeCriteria)) {
            $this->nodeTypeCriteria = NodeTypeCriteria::fromFilterString($nodeTypeCriteria);
        } elseif ($nodeTypeCriteria instanceof NodeTypeCriteria) {
            $this->nodeTypeCriteria = $nodeTypeCriteria;
        }
    }

    public function process(Nodes $nodes): Nodes
    {
        $findAncestorFilter = FindAncestorNodesFilter::create(
            $this->nodeTypeCriteria
        );

        $ancestorNodesArray = [];
        foreach ($nodes as $node) {
            $subgraph = $this->crRegistry->subgraphForNode($node);
            $ancestors = $subgraph->findAncestorNodes(
                $node->nodeAggregateId,
                $findAncestorFilter
            );
            $ancestorNodesArray[] = [$node, ...iterator_to_array($ancestors)];
        }
        return Nodes::fromArray(array_merge(...$ancestorNodesArray));
    }
}
