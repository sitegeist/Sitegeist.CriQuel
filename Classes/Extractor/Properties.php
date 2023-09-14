<?php

namespace Sitegeist\CriQuel\Extractor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Sitegeist\CriQuel\ExtractorInterface;

class Properties implements ExtractorInterface
{
    public function __construct(protected string $name)
    {
    }

    public function apply(Nodes $nodes): array
    {
        $result = [];
        foreach ($nodes as $key => $node) {
            $result[$key] = $node->getProperty($this->name);
        }
        return $result;
    }
}
