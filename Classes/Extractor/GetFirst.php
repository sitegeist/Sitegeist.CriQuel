<?php

namespace Sitegeist\CriQuel\Extractor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Sitegeist\CriQuel\ExtractorInterface;

class GetFirst implements ExtractorInterface
{
    public function extract(Nodes $nodes): ?Node
    {
        return $nodes->first();
    }
}
