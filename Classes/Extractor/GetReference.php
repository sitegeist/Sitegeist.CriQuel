<?php

namespace Sitegeist\CriQuel\Extractor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindReferencesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\Pagination\Pagination;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\Criteria\PropertyValueCriteriaInterface;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\PropertyValueCriteriaParser;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepository\Core\Projection\ContentGraph\NodeTypeConstraints;
use Neos\ContentRepository\Core\Projection\ContentGraph\Reference;
use Neos\ContentRepository\Core\SharedModel\Node\ReferenceName;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;
use Sitegeist\CriQuel\ExtractorInterface;

class GetReference implements ExtractorInterface
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $contentRepositoryRegistry;

    protected ?ReferenceName $referenceName = null;

    public function __construct(string|ReferenceName $referenceName)
    {
        if (is_string($referenceName)) {
            $this->referenceName = ReferenceName::fromString($referenceName);
        } elseif ($referenceName instanceof ReferenceName) {
            $this->referenceName = $referenceName;
        }
    }

    public function extract(Nodes $nodes): ?Reference
    {
        $findReferencesFilter = FindReferencesFilter::create(
            referenceName: $this->referenceName,
            pagination: Pagination::fromLimitAndOffset(1, 0)
        );
        foreach ($nodes as $node) {
            $subgraph = $this->contentRepositoryRegistry->subgraphForNode($node);
            $references = $subgraph->findReferences($node->nodeAggregateId, $findReferencesFilter);
            foreach ($references as $reference) {
                return $reference;
            }
        }
        return null;
    }
}
