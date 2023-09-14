<?php

namespace Sitegeist\CriQuel\Extractor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Sitegeist\CriQuel\ExtractorInterface;

class Property implements ExtractorInterface
{
    public function __construct(protected string $name)
    {
    }

    public function apply(Nodes $nodes): mixed
    {
        return $nodes->first()?->getProperty($this->name);
    }
}
