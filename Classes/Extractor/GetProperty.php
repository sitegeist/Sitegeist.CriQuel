<?php

namespace Sitegeist\CriQuel\Extractor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Sitegeist\CriQuel\ExtractorInterface;

class GetProperty implements ExtractorInterface
{
    public function __construct(protected string $name)
    {
    }

    public function extract(Nodes $nodes): mixed
    {
        return $nodes->first()?->getProperty($this->name);
    }
}
