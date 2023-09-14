<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel\Operations;

use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\NodeAccess\FlowQueryOperations\CreateNodeHashTrait;
use Sitegeist\CriQuel\OperationInterface;

class Remove implements OperationInterface
{
    use CreateNodeHashTrait;

    private array $hashesToRemove = [];
    public function __construct(Nodes|Node ...$items)
    {
        $nodes = Nodes::createEmpty();
        foreach ($items as $item) {
            if ($item instanceof Node) {
                $nodes = $nodes->merge(Nodes::fromArray([$item]));
            } elseif ($item instanceof Nodes) {
                $nodes = $nodes->merge($item);
            }
        }
        foreach ($nodes as $node) {
            $hash = $this->createNodeHash($node);
            $hashesToRemove[$hash] = $hash;
        }
    }

    public function apply(Nodes $nodes): Nodes
    {
        $result = [];
        foreach ($nodes as $node) {
            $hash = $this->createNodeHash($node);
            if (!in_array($hash, $this->hashesToRemove)) {
                $result[] = $node;
            }
        }
        return Nodes::fromArray($result);
    }
}
