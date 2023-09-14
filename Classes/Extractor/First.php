<?php

namespace Sitegeist\CriQuel\Extractor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Sitegeist\CriQuel\ExtractorInterface;

class First implements ExtractorInterface
{
    public function apply(Nodes $nodes): Node|null
    {
        return $nodes->first();
    }
}
