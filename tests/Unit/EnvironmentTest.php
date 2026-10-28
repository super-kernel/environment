<?php

declare(strict_types=1);

namespace SuperKernel\Environment\Tests\Unit;

use PHPUnit\Framework\TestCase;
use stdClass;
use SuperKernel\Environment\Environment;
use SuperKernel\Environment\Exception\InvalidEnvironmentValueException;
use SuperKernel\Environment\Exception\InvalidPropertyNameException;
use SuperKernel\Environment\Exception\InvalidPropertySourceException;
use SuperKernel\Environment\PropertySource\ArrayPropertySource;

final class EnvironmentTest extends TestCase
{
    public function testLaterSourcesOverrideEarlierSourcesAndPreserveExplicitNull(): void
    {
        $environment = new Environment([
            new ArrayPropertySource('defaults', [
                'app.name'   => 'first',
                'app.masked' => 'visible',
            ]),
            new ArrayPropertySource('override', [
                'app.name'   => 'second',
                'app.masked' => null,
            ]),
        ]);

        self::assertSame('second', $environment->getString('app.name'));
        self::assertTrue($environment->has('app.masked'));
        self::assertNull($environment->get('app.masked'));
        self::assertFalse($environment->has('app.missing'));
    }

    public function testConvertsSupportedScalarTypesStrictly(): void
    {
        $environment = new Environment([
            new ArrayPropertySource('values', [
                'value.string' => 42,
                'value.int'    => '-7',
                'value.float'  => '1.25',
                'value.true'   => 'YES',
                'value.false'  => 0,
            ]),
        ]);

        self::assertSame('42', $environment->getString('value.string'));
        self::assertSame(-7, $environment->getInt('value.int'));
        self::assertSame(1.25, $environment->getFloat('value.float'));
        self::assertTrue($environment->getBool('value.true'));
        self::assertFalse($environment->getBool('value.false'));
        self::assertNull($environment->getBool('value.missing'));
    }

    public function testRejectsInvalidExplicitValuesWithoutDisclosingTheirContents(): void
    {
        $environment = new Environment([
            new ArrayPropertySource('values', [
                'secret.flag' => 'private-value',
            ]),
        ]);

        try {
            $environment->getBool('secret.flag');
            self::fail('An invalid explicit boolean must fail.');
        } catch (InvalidEnvironmentValueException $exception) {
            self::assertStringContainsString('secret.flag', $exception->getMessage());
            self::assertStringNotContainsString('private-value', $exception->getMessage());
        }
    }

    public function testRejectsNonCanonicalPropertyNames(): void
    {
        $this->expectException(InvalidPropertyNameException::class);

        new Environment([])->get('APP_NAME');
    }

    public function testRejectsNonScalarArraySourceValues(): void
    {
        $this->expectException(InvalidPropertySourceException::class);

        new ArrayPropertySource('invalid', [
            'app.value' => new stdClass(),
        ]);
    }
}
