<?php

namespace Sitegeist\CriQuel\Extractor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Sitegeist\CriQuel\ExtractorInterface;

class GetCount implements ExtractorInterface
{
    public function apply(Nodes $nodes): int
    {
        return $nodes->count();
    }
}
