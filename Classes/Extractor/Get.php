<?php

namespace Sitegeist\CriQuel\Extractor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Sitegeist\CriQuel\ExtractorInterface;

class Get implements ExtractorInterface
{
    public function apply(Nodes $nodes): Nodes
    {
        return $nodes;
    }
}
