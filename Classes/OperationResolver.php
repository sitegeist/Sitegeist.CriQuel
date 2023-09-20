<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel;

class OperationResolver implements OperationResolverInterface
{
    /**
     * @var array<string, ProcessorInterface|ExtractorInterface>
     */
    private array $operations;

    /**
     * @param mixed[] $settings
     * @return void
     */
    public function injectSettings(array $settings): void
    {
        $this->operations = $settings['operations'] ?? [];
    }

    /**
     * @param mixed[] $arguments
     */
    public function resolve(string $name, array $arguments): ProcessorInterface|ExtractorInterface
    {
        if (array_key_exists($name, $this->operations)) {
            return new $this->operations[$name](...$arguments);
        }
        throw new \InvalidArgumentException(sprintf('Operation "%s" could not be resolved', $name));
    }
}
