<?php

declare(strict_types=1);

namespace SuperKernel\Environment\PropertySource;

use SuperKernel\Environment\Contract\PropertySourceInterface;
use SuperKernel\Environment\Exception\InvalidPropertySourceException;
use SuperKernel\Environment\Naming\PropertyName;

use function array_key_exists;
use function get_debug_type;
use function is_scalar;
use function is_string;
use function sprintf;

final readonly class ArrayPropertySource implements PropertySourceInterface
{
    /**
     * @param non-empty-string                          $name
     * @param array<string, string|int|float|bool|null> $values
     */
    public function __construct(private string $name, private array $values)
    {
        if ('' === $name) {
            throw new InvalidPropertySourceException('A property source name must not be empty.');
        }

        foreach ($values as $propertyName => $value) {
            if (!is_string($propertyName)) {
                throw new InvalidPropertySourceException(sprintf(
                    'Property source keys must be canonical strings; %s provided.',
                    get_debug_type($propertyName),
                ));
            }

            PropertyName::assertValid($propertyName);

            if (null !== $value && !is_scalar($value)) {
                throw new InvalidPropertySourceException(sprintf(
                    'Property source value for %s must be scalar or null; %s provided.',
                    $propertyName,
                    get_debug_type($value),
                ));
            }
        }
    }

    public function name(): string
    {
        return $this->name;
    }

    public function has(string $name): bool
    {
        PropertyName::assertValid($name);

        return array_key_exists($name, $this->values);
    }

    public function get(string $name): string|int|float|bool|null
    {
        PropertyName::assertValid($name);

        return $this->values[$name] ?? null;
    }
}
