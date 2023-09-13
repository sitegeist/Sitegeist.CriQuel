<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel;

use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\Eel\ProtectedContextAwareInterface;
use Neos\Flow\Annotations as Flow;
use Traversable;

class Query implements ProtectedContextAwareInterface, \IteratorAggregate
{
    #[FLow\Inject]
    protected OperationResolverInterface $operationResolver;

    protected Nodes $nodes;

    private function __construct(Nodes $nodes)
    {
        $this->nodes = $nodes;
    }

    public static function create(Nodes|Node|Query ...$items): Query
    {
        $nodes = Nodes::createEmpty();
        foreach ($items as $item) {
            if ($item instanceof Node) {
                $nodes = $nodes->merge(Nodes::fromArray([$item]));
            } elseif ($item instanceof Nodes) {
                $nodes = $nodes->merge($item);
            } elseif ($item instanceof Query) {
                $nodes = $nodes->merge($item->nodes);
            }
        }
        return new Query($nodes);
    }

    public function apply(OperationInterface $operation): Query
    {
        return new Query($operation->apply($this->nodes));
    }

    public function get(): Nodes
    {
        return $this->nodes;
    }

    public function first(): ?Node
    {
        return $this->nodes->first();
    }

    public function __call(string $methodName, array $arguments): Query
    {
        $operation = $this->operationResolver->resolve($methodName, $arguments);
        return $this->apply($operation);
    }

    public function getIterator(): Traversable
    {
        return $this->nodes;
    }

    public function allowsCallOfMethod($methodName)
    {
        return !in_array($methodName, ['apply', 'getIterator', '__call', 'create]']);
    }
}
