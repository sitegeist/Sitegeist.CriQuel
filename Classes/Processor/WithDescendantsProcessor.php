<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel\Processor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindSubtreeFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\NodeType\NodeTypeCriteria;
use Neos\ContentRepository\Core\Projection\ContentGraph\Subtree;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Sitegeist\CriQuel\ProcessorInterface;
use Sitegeist\CriQuel\Trait\FlattenSubtreeTrait;

final class WithDescendantsProcessor implements ProcessorInterface
{
    use FlattenSubtreeTrait;

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
        $filter = FindSubtreeFilter::create(
            $this->nodeTypeCriteria,
        );
        $result = Nodes::createEmpty();
        foreach ($nodes as $node) {
            $subgraph = $this->crRegistry->subgraphForNode($node);
            $subtree = $subgraph->findSubtree($node->nodeAggregateId, $filter);
            if ($subtree instanceof Subtree) {
                $result = $result->merge($this->flattenSubtree($subtree));
            }
        }
        return $result;
    }
}
