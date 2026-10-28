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

    private function __construct()
    {
    }

    /**
     * @return non-empty-string
     */
    public static function environmentVariable(string $name): string
    {
        self::assertValid($name);

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

        throw new InvalidPropertyNameException(
            'Property names must use non-empty lowercase dotted or kebab notation.',
        );
    }
}
