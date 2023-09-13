<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel\Operations;

use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindReferencesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindSubtreeFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Flow\Annotations as Flow;
use Sitegeist\CriQuel\OperationInterface;
use Sitegeist\Taxonomy\Service\TaxonomyService;

final class DescendantsOperation implements OperationInterface
{
    use FlattenSubtreeTrait;

    #[Flow\Inject]
    protected ContentRepositoryRegistry $crRegistry;

    protected ?string $nodeTypeConstraints;
    protected ?int $maximumLevels;

    public function __construct(string $nodeTypeConstraints = null, int $maximumLevels = null)
    {
        $this->nodeTypeConstraints = $nodeTypeConstraints;
        $this->maximumLevels = $maximumLevels;
    }

    public function apply(Nodes $nodes): Nodes
    {
        $result = Nodes::createEmpty();
        $filter = FindSubtreeFilter::create(
            $this->nodeTypeConstraints,
            $this->maximumLevels
        );
        foreach ($nodes as $node) {
            $subgraph = $this->crRegistry->subgraphForNode($node);
            $subtree = $subgraph->findSubtree($node->nodeAggregateId, $filter);
            if ($subtree) {
                foreach ($subtree->children as $child) {
                    $result = $result->merge($this->flattenSubtree($child));
                }
            }
        }
        return $result;
    }
}
