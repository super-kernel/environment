<?php

declare(strict_types=1);

namespace SuperKernel\Environment\Precedence;

use SuperKernel\Environment\Contract\PropertySourceInterface;
use SuperKernel\Environment\Exception\InvalidPropertyNameException;
use SuperKernel\Environment\Naming\PropertyName;

use function array_reverse;

/**
 * Reads a canonical property from the highest-precedence source that contains it.
 *
 * @internal
 */
final class PropertyLookup
{
    /**
     * Returns the raw value, or null when no source contains the property or the winning source stores null.
     *
     * @param list<PropertySourceInterface> $sources ordered from lowest to highest precedence
     *
     * @throws InvalidPropertyNameException when the name is not canonical lowercase dotted or kebab notation
     */
    public static function get(array $sources, string $name): string|int|float|bool|null
    {
        PropertyName::assertValid($name);

        foreach (array_reverse($sources) as $source) {
            if ($source->has($name)) {
                return $source->get($name);
            }
        }

        return null;
    }
}
