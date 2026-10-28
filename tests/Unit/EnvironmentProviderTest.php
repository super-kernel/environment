<?php

declare(strict_types=1);

namespace SuperKernel\Environment\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use SuperKernel\Attribute\FactoryProvider;
use SuperKernel\Environment\Contract\EnvironmentInterface;
use SuperKernel\Environment\Environment;
use SuperKernel\Environment\Provider\EnvironmentProvider;

final class EnvironmentProviderTest extends TestCase
{
    public function testProvidesTheProcessBackedEnvironmentContract(): void
    {
        $reflection = new ReflectionClass(EnvironmentProvider::class);
        $attributes = $reflection->getAttributes(FactoryProvider::class);
        $environment = new EnvironmentProvider()();

        self::assertTrue($reflection->isFinal());
        self::assertTrue($reflection->isReadOnly());
        self::assertCount(1, $attributes);
        self::assertSame(EnvironmentInterface::class, $attributes[0]->newInstance()->className);
        self::assertInstanceOf(Environment::class, $environment);
    }
}
