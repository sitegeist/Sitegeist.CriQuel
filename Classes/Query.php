<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel;

use Neos\Cache\Frontend\StringFrontend;
use Neos\Cache\Frontend\VariableFrontend;
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
    #[Flow\InjectCache(identifier: 'Neos_Fusion_Content')]
    protected StringFrontend $cache;

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

    public function chain(ProcessorInterface $operation): Query
    {
        return new Query($operation->process($this->nodes));
    }

    /**
     * @todo find a way to specify return type based on passed extractor object
     *       and sometimes arguments like `new Extractor\Property('title')`
     */
    public function extract(ExtractorInterface $extractor): mixed
    {
        return $extractor->extract($this->nodes);
    }

    /**
     * This duplicates logic from the GetNodes extractor for beeing type safe accessible in php
     * @see \Sitegeist\CriQuel\Extractor\GetNodesExtractor
     */
    public function get(): Nodes
    {
        return $this->nodes;
    }

    /**
     * This duplicates logic from the GetNode extractor for beeing type safe accessible in php
     * @see \Sitegeist\CriQuel\Extractor\GetNodeExtractor
     */
    public function first(): ?Node
    {
        return $this->nodes->first();
    }

    /**
     * @param mixed[] $arguments
     */
    public function __call(string $methodName, array $arguments): mixed
    {
        $operation = $this->operationResolver->resolve($methodName, $arguments);
        if ($operation instanceof ProcessorInterface) {
            return $this->chain($operation);
        }
        return $this->extract($operation);
    }

    public function getIterator(): Traversable
    {
        return $this->nodes;
    }

    public function allowsCallOfMethod($methodName)
    {
        return !in_array($methodName, ['process', 'extract', 'getIterator', '__call', 'create]'], true);
    }
}
