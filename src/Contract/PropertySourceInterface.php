<?php

declare(strict_types=1);

namespace SuperKernel\Environment\Contract;

interface PropertySourceInterface
{
    /**
     * Returns the stable diagnostic name of this source.
     *
     * @return non-empty-string
     */
    public function name(): string;

    /**
     * Reports whether this source contains the canonical property name, including a stored null value.
     */
    public function has(string $name): bool;

    /**
     * Returns the raw scalar value, or null when the property is absent or explicitly null.
     */
    public function get(string $name): string|int|float|bool|null;
}
