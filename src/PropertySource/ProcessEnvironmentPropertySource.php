<?php

declare(strict_types=1);

namespace SuperKernel\Environment\PropertySource;

use SuperKernel\Environment\Contract\PropertySourceInterface;
use SuperKernel\Environment\Naming\PropertyName;

use function getenv;

final readonly class ProcessEnvironmentPropertySource implements PropertySourceInterface
{
    public function name(): string
    {
        return 'process-environment';
    }

    public function has(string $name): bool
    {
        return false !== getenv(PropertyName::environmentVariable($name));
    }

    public function get(string $name): ?string
    {
        $value = getenv(PropertyName::environmentVariable($name));

        return false === $value ? null : $value;
    }
}
