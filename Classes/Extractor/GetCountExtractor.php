<?php

namespace Sitegeist\CriQuel\Extractor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Sitegeist\CriQuel\ExtractorInterface;

class GetCountExtractor implements ExtractorInterface
{
    public function extract(Nodes $nodes): int
    {
        return $nodes->count();
    }
}
