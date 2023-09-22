<?php

namespace Sitegeist\CriQuel\Extractor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Sitegeist\CriQuel\ExtractorInterface;

class GetProperties implements ExtractorInterface
{
    public function __construct(protected string $name)
    {
    }

    /**
     * @return mixed[]
     */
    public function extract(Nodes $nodes): array
    {
        $result = [];
        foreach ($nodes as $key => $node) {
            $result[$key] = $node->getProperty($this->name);
        }
        return $result;
    }
}
