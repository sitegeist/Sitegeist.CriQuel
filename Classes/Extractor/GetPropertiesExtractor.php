<?php

namespace Sitegeist\CriQuel\Extractor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepository\Core\SharedModel\Node\PropertyName;
use Sitegeist\CriQuel\ExtractorInterface;

class GetPropertiesExtractor implements ExtractorInterface
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

    /**
     * @return mixed[]
     */
    public function extract(Nodes $nodes): array
    {
        $result = [];
        foreach ($nodes as $key => $node) {
            $result[$key] = $node->getProperty($this->name->value);
        }
        return $result;
    }
}
