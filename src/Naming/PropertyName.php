<?php

declare(strict_types=1);

namespace SuperKernel\Environment\Naming;

use SuperKernel\Environment\Exception\InvalidPropertyNameException;

use function preg_match;
use function str_replace;
use function strtoupper;

/**
 * Canonical property-name validation and external variable-name mapping.
 *
 * @internal
 */
final class PropertyName
{
    private const string PATTERN = '/\A[a-z][a-z0-9]*(?:[.-][a-z0-9]+)*\z/';

    private const string INVALID_MESSAGE = 'Property names must use non-empty lowercase dotted or kebab notation.';

    /**
     * @return non-empty-string
     */
    public static function environmentVariable(string $name): string
    {
        if (1 !== preg_match(self::PATTERN, $name)) {
            throw new InvalidPropertyNameException(self::INVALID_MESSAGE);
        }

        return strtoupper(str_replace([
            '.',
            '-',
        ], '_', $name));
    }

    public static function assertValid(string $name): void
    {
        if (1 === preg_match(self::PATTERN, $name)) {
            return;
        }

        throw new InvalidPropertyNameException(self::INVALID_MESSAGE);
    }
}
