<?php

declare(strict_types=1);

namespace Sitegeist\CriQuel;

/**
 * Type safe accessing of values from nested arrays
 */
class ValueAccessor
{
    public function __construct(
        public readonly mixed $value,
    ) {
    }

    private function createTypeError($message): \TypeError
    {
        return new \TypeError(get_debug_type($this->value) . ' ' . $message);
    }

    public function int(): int
    {
        if (is_int($this->value)) {
            return $this->value;
        }
        throw $this->createTypeError("is not an integer");
    }

    public function float(): float
    {
        if (is_float($this->value)) {
            return $this->value;
        }
        throw $this->createTypeError("is not a float");
    }

    public function number(): int|float
    {
        if (is_int($this->value) || is_float($this->value)) {
            return $this->value;
        }
        throw $this->createTypeError("is not a number");
    }

    public function string(): string
    {
        if (is_string($this->value)) {
            return $this->value;
        }
        throw $this->createTypeError("is not a string");
    }

    /**
     * @return class-string
     */
    public function classString(): string
    {
        if (is_string($this->value) && class_exists($this->value)) {
            return $this->value;
        }
        throw $this->createTypeError("is not a class-string");
    }

    public function array(): array
    {
        if (is_array($this->value)) {
            return $this->value;
        }
        throw $this->createTypeError("is not an array");
    }

    /**
     * @template T
     * @param class-string<T> $type
     * @return object&T
     */
    public function object(string $type): object
    {
        if (is_a($this->value, $type, false)) {
            return $this->value;
        }
        throw $this->createTypeError(sprintf('is not of type class %s', $type));
    }

    public function intOrNull(): ?int
    {
        if (is_int($this->value) || is_null($this->value)) {
            return $this->value;
        }
        throw $this->createTypeError("is not an integer or null");
    }

    public function floatOrNull(): ?float
    {
        if (is_float($this->value) || is_null($this->value)) {
            return $this->value;
        }
        throw $this->createTypeError("is not a float or null");
    }

    public function numberOrNull(): null|int|float
    {
        if (is_int($this->value) || is_float($this->value) || is_null($this->value)) {
            return $this->value;
        }
        throw $this->createTypeError("is not a number or null");
    }

    public function stringOrNull(): ?string
    {
        if (is_string($this->value) || is_null($this->value)) {
            return $this->value;
        }
        throw $this->createTypeError("is not a string or null");
    }

    /**
     * @return class-string|null
     */
    public function classStringOrNull(): ?string
    {
        if ((is_string($this->value) && class_exists($this->value)) || is_null($this->value)) {
            return $this->value;
        }
        throw $this->createTypeError("is not a class-string");
    }

    public function arrayOrNull(): ?array
    {
        if (is_array($this->value) || is_null($this->value)) {
            return $this->value;
        }
        throw $this->createTypeError("is not an array or null");
    }

    /**
     * @template T
     * @param class-string<T> $type
     * @return (object&T)|null
     */
    public function objectOrNull(string $type): ?object
    {
        if (is_a($this->value, $type, false) || is_null($this->value)) {
            return $this->value;
        }
        throw $this->createTypeError(sprintf('is not of type class %s or null', $type));
    }
}
