<?php

namespace Sitegeist\CriQuel\Extractor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepository\Core\SharedModel\Node\PropertyName;
use Sitegeist\CriQuel\ExtractorInterface;

class GetPropertyExtractor implements ExtractorInterface
{
    protected PropertyName $name;
    public function __construct(string|PropertyName $name)
    {
        if (is_string($name)) {
            $this->name = PropertyName::fromString($name);
        } else {
            $this->name = $name;
        }
    }

    public function extract(Nodes $nodes): mixed
    {
        return $nodes->first()?->getProperty($this->name->value);
    }
}
