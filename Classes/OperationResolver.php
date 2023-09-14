<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel;

use Sitegeist\CriQuel\ProcessorInterface;

class OperationResolver implements OperationResolverInterface
{
    private array $operations;

    public function injectSettings(array $settings)
    {
        $this->operations = $settings['operations'] ?? [];
    }

    public function resolve(string $name, array $arguments): ProcessorInterface|ExtractorInterface
    {
        if (array_key_exists($name, $this->operations)) {
            return new $this->operations[$name](...$arguments);
        }
        throw new \InvalidArgumentException(sprintf('Operation "%s" could not be resolved', $name));
    }
}
