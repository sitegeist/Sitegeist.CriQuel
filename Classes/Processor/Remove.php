<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel\Processor;

use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\NodeAccess\FlowQueryOperations\CreateNodeHashTrait;
use Sitegeist\CriQuel\ProcessorInterface;
use Sitegeist\CriQuel\Query;

class Remove implements ProcessorInterface
{
    use CreateNodeHashTrait;

    /**
     * @var array<string, string>
     */
    private array $hashesToRemove = [];

    public function __construct(Nodes|Node|Query ...$items)
    {
        $nodesToRemove = Nodes::createEmpty();
        foreach ($items as $item) {
            if ($item instanceof Node) {
                $nodesToRemove = $nodesToRemove->merge(Nodes::fromArray([$item]));
            } elseif ($item instanceof Nodes) {
                $nodesToRemove = $nodesToRemove->merge($item);
            } elseif ($item instanceof Query) {
                $nodesToRemove = $nodesToRemove->merge($item->nodes);
            }
        }
        foreach ($nodesToRemove as $node) {
            $hash = $this->createNodeHash($node);
            $this->hashesToRemove[$hash] = $hash;
        }
    }

    public function apply(Nodes $nodes): Nodes
    {
        $filteredNodes = [];
        foreach ($nodes as $node) {
            $hash = $this->createNodeHash($node);
            if (!in_array($hash, $this->hashesToRemove, true)) {
                $filteredNodes[] = $node;
            }
        }
        return Nodes::fromArray($filteredNodes);
    }
}
