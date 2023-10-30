<?php

namespace Sitegeist\CriQuel\Extractor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Sitegeist\CriQuel\ExtractorInterface;

class GetNodesExtractor implements ExtractorInterface
{
    public function extract(Nodes $nodes): Nodes
    {
        return $nodes;
    }
}
