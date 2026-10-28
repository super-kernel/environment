<?php

declare(strict_types=1);

namespace SuperKernel\Environment\Provider;

use SuperKernel\Attribute\FactoryProvider;
use SuperKernel\Environment\Contract\EnvironmentInterface;
use SuperKernel\Environment\Environment;
use SuperKernel\Environment\PropertySource\ProcessEnvironmentPropertySource;

#[FactoryProvider(EnvironmentInterface::class)]
final readonly class EnvironmentProvider
{
    public function __invoke(): EnvironmentInterface
    {
        return new Environment([
            new ProcessEnvironmentPropertySource(),
        ]);
    }
}
