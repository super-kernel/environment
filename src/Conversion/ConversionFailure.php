<?php

declare(strict_types=1);

namespace SuperKernel\Environment\Conversion;

use SuperKernel\Environment\Exception\InvalidEnvironmentValueException;

use function get_debug_type;
use function sprintf;

/**
 * Builds typed-conversion failures that name the property and native input type but never the configured value.
 *
 * @internal
 */
final class ConversionFailure
{
    /**
     * @param string $expected the expected kind with its article, for example `an integer`
     */
    public static function invalidValue(
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
