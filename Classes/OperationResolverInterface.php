<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel;

interface OperationResolverInterface
{
    /**
     * @param mixed[] $arguments
     */
    public function resolve(string $name, array $arguments): ProcessorInterface|ExtractorInterface;
}
