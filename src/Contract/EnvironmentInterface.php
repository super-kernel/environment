<?php

declare(strict_types=1);

namespace SuperKernel\Environment\Contract;

use SuperKernel\Environment\Exception\InvalidEnvironmentValueException;
use SuperKernel\Environment\Exception\InvalidPropertyNameException;

interface EnvironmentInterface
{
    /**
     * @throws InvalidPropertyNameException when the name is not canonical lowercase dotted or kebab notation
     */
    public function has(string $name): bool;

    /**
     * @throws InvalidPropertyNameException when the name is not canonical lowercase dotted or kebab notation
     */
    public function get(string $name): string|int|float|bool|null;

    /**
     * @throws InvalidPropertyNameException when the name is not canonical lowercase dotted or kebab notation
     */
    public function getString(string $name): ?string;

    /**
     * @throws InvalidEnvironmentValueException when an explicit value is not an integer
     * @throws InvalidPropertyNameException     when the name is not canonical lowercase dotted or kebab notation
     */
    public function getInt(string $name): ?int;

    /**
     * @throws InvalidEnvironmentValueException when an explicit value is not a finite number
     * @throws InvalidPropertyNameException     when the name is not canonical lowercase dotted or kebab notation
     */
    public function getFloat(string $name): ?float;

    /**
     * @throws InvalidEnvironmentValueException when an explicit value is not a recognized boolean
     * @throws InvalidPropertyNameException     when the name is not canonical lowercase dotted or kebab notation
     */
    public function getBool(string $name): ?bool;
}
