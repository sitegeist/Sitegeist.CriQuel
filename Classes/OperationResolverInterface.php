<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel;

interface OperationResolverInterface
{
    public function resolve(string $name, array $arguments): OperationInterface;
}
