<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel\Processor;

use Neos\ContentRepository\Core\NodeType\NodeTypeNames;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindAncestorNodesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindReferencesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepository\Core\Projection\ContentGraph\NodeTypeConstraints;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Flow\Annotations as Flow;
use Sitegeist\CriQuel\ProcessorInterface;
use Sitegeist\Taxonomy\Service\TaxonomyService;

class Ancestors implements ProcessorInterface
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $crRegistry;

    protected ?string $nodeTypeConstraints;

    public function __construct(string $nodeTypeConstraints = null)
    {
        $this->nodeTypeConstraints = $nodeTypeConstraints;
    }

    public function apply(Nodes $nodes): Nodes
    {
        $findAncestorFilter = FindAncestorNodesFilter::create(
            $this->nodeTypeConstraints
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
