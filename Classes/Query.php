<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel;

use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\Projection\ContentGraph\Nodes;
use Neos\Eel\ProtectedContextAwareInterface;
use Neos\Flow\Annotations as Flow;
use Traversable;

/**
 * @implements \IteratorAggregate<int, Node>
 */
class Query implements ProtectedContextAwareInterface, \IteratorAggregate
{
    #[FLow\Inject]
    protected OperationResolverInterface $operationResolver;

    public function __construct(public readonly Nodes $nodes)
    {
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

    public function process(ProcessorInterface $operation): Query
    {
        return new Query($operation->apply($this->nodes));
    }

    /**
     * @todo find a way to specify return type based on passed extractor object
     *       and sometimes arguments like `new Extractor\Property('title')`
     */
    public function extract(ExtractorInterface $extractor): mixed
    {
        return $extractor->apply($this->nodes);
    }

    public function get(): Nodes
    {
        return $this->nodes;
    }

    /**
     * @param mixed[] $arguments
     */
    public function __call(string $methodName, array $arguments): mixed
    {
        $operation = $this->operationResolver->resolve($methodName, $arguments);
        if ($operation instanceof ProcessorInterface) {
            return $this->process($operation);
        }
        return $this->extract($operation);
    }

    public function getIterator(): Traversable
    {
        return $this->nodes;
    }

    public function allowsCallOfMethod($methodName)
    {
        return !in_array($methodName, ['process', 'extract', 'getIterator', '__call', 'create]']);
    }
}
