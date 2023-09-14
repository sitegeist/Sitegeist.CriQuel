<?php

namespace Sitegeist\CriQuel\Extractor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Sitegeist\CriQuel\ExtractorInterface;

class Last implements ExtractorInterface
{
    public function apply(Nodes $nodes): Node|null
    {
        $array = iterator_to_array($nodes);
        $index = array_key_last($array);
        return $array[$index];
    }
}
