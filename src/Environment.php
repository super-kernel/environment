<?php

declare(strict_types=1);

namespace SuperKernel\Environment;

use SuperKernel\Environment\Contract\EnvironmentInterface;
use SuperKernel\Environment\Contract\PropertySourceInterface;
use SuperKernel\Environment\Exception\InvalidEnvironmentValueException;
use SuperKernel\Environment\Exception\InvalidPropertySourceException;
use SuperKernel\Environment\Naming\PropertyName;

use function array_any;
use function array_is_list;
use function array_reverse;
use function filter_var;
use function get_debug_type;
use function is_bool;
use function is_finite;
use function is_float;
use function is_int;
use function is_string;
use function sprintf;
use function strtolower;
use function trim;

use const FILTER_VALIDATE_FLOAT;
use const FILTER_VALIDATE_INT;

final readonly class Environment implements EnvironmentInterface
{
    /** @var list<PropertySourceInterface> */
    private array $sources;

    /**
     * @param list<PropertySourceInterface> $sources ordered from lowest to highest precedence
     */
    public function __construct(array $sources)
    {
        if (!array_is_list($sources)) {
            throw new InvalidPropertySourceException('Environment property sources must be a list.');
        }

        foreach ($sources as $source) {
            if (!$source instanceof PropertySourceInterface) {
                throw new InvalidPropertySourceException(sprintf(
                    'Environment property sources must implement %s; %s provided.',
                    PropertySourceInterface::class,
                    get_debug_type($source),
                ));
            }
        }

        $this->sources = $sources;
    }

    public function has(string $name): bool
    {
        PropertyName::assertValid($name);

        return array_any(array_reverse($this->sources), fn($source) => $source->has($name));
    }

    public function get(string $name): string|int|float|bool|null
    {
        PropertyName::assertValid($name);

        foreach (array_reverse($this->sources) as $source) {
            if ($source->has($name)) {
                return $source->get($name);
            }
        }

        return null;
    }

    public function getString(string $name): ?string
    {
        $value = $this->get($name);

        if (null === $value || is_string($value)) {
            return $value;
        }

        if (true === $value) {
            return 'true';
        }

        if (false === $value) {
            return 'false';
        }

        return (string) $value;
    }

    public function getInt(string $name): ?int
    {
        $value = $this->get($name);

        if (null === $value || is_int($value)) {
            return $value;
        }

        if (is_string($value)) {
            $converted = filter_var($value, FILTER_VALIDATE_INT);

            if (false !== $converted) {
                return $converted;
            }
        }

        throw $this->invalidValue($name, 'an integer', $value);
    }

    public function getFloat(string $name): ?float
    {
        $value = $this->get($name);

        if (null === $value) {
            return null;
        }

        if (is_int($value) || is_float($value)) {
            $converted = (float) $value;
        } elseif (is_string($value)) {
            $converted = filter_var($value, FILTER_VALIDATE_FLOAT);
        } else {
            $converted = false;
        }

        if (false !== $converted && is_finite($converted)) {
            return $converted;
        }

        throw $this->invalidValue($name, 'a finite number', $value);
    }

    public function getBool(string $name): ?bool
    {
        $value = $this->get($name);

        if (null === $value || is_bool($value)) {
            return $value;
        }

        if (0 === $value || 1 === $value) {
            return 1 === $value;
        }

        if (is_string($value)) {
            return match (strtolower(trim($value))) {
                '1', 'true', 'yes', 'on'  => true,
                '0', 'false', 'no', 'off' => false,
                default                   => throw $this->invalidValue($name, 'a boolean', $value),
            };
        }

        throw $this->invalidValue($name, 'a boolean', $value);
    }

    private function invalidValue(
        string $name,
        string $expected,
        mixed  $value,
    ): InvalidEnvironmentValueException
    {
        return new InvalidEnvironmentValueException(sprintf(
            'Environment property %s must be %s; %s provided.',
            $name,
            $expected,
            get_debug_type($value),
        ));
    }
}
