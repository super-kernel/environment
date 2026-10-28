<?php

declare(strict_types=1);

namespace SuperKernel\Environment\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SuperKernel\Environment\Exception\PropertySourceLoadingException;
use SuperKernel\Environment\PropertySource\DotenvPropertySource;
use SuperKernel\Environment\PropertySource\ProcessEnvironmentPropertySource;

use function file_put_contents;
use function getenv;
use function putenv;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

final class PropertySourceTest extends TestCase
{
    public function testReadsCanonicalPropertiesFromTheProcessEnvironment(): void
    {
        $previous = getenv('SUPER_KERNEL_TEST_FLAG');

        try {
            putenv('SUPER_KERNEL_TEST_FLAG=enabled');
            $source = new ProcessEnvironmentPropertySource();

            self::assertTrue($source->has('super-kernel.test.flag'));
            self::assertSame('enabled', $source->get('super-kernel.test.flag'));
        } finally {
            false === $previous
                ? putenv('SUPER_KERNEL_TEST_FLAG')
                : putenv('SUPER_KERNEL_TEST_FLAG=' . $previous);
        }
    }

    public function testReadsADotenvSnapshotWithoutMutatingTheProcessEnvironment(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'super-kernel-env-');
        self::assertIsString($path);
        file_put_contents($path, "SUPER_KERNEL_TEST_FLAG=dotenv\n");
        $previous = getenv('SUPER_KERNEL_TEST_FLAG');

        try {
            putenv('SUPER_KERNEL_TEST_FLAG');
            $source = new DotenvPropertySource($path, false);

            self::assertSame('dotenv', $source->get('super-kernel.test.flag'));
            self::assertFalse(getenv('SUPER_KERNEL_TEST_FLAG'));
        } finally {
            false === $previous
                ? putenv('SUPER_KERNEL_TEST_FLAG')
                : putenv('SUPER_KERNEL_TEST_FLAG=' . $previous);
            unlink($path);
        }
    }

    public function testAllowsAMissingOptionalDotenvFile(): void
    {
        $source = new DotenvPropertySource(sys_get_temp_dir() . '/missing-super-kernel-env', true);

        self::assertFalse($source->has('app.value'));
        self::assertNull($source->get('app.value'));
    }

    public function testRejectsAMissingRequiredDotenvFile(): void
    {
        $this->expectException(PropertySourceLoadingException::class);

        new DotenvPropertySource(sys_get_temp_dir() . '/missing-required-super-kernel-env', false);
    }

    public function testWrapsMalformedDotenvContent(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'super-kernel-env-');
        self::assertIsString($path);
        file_put_contents($path, "NOT A DOTENV DECLARATION\n");

        try {
            new DotenvPropertySource($path, false);
            self::fail('Malformed dotenv content must fail.');
        } catch (PropertySourceLoadingException $exception) {
            self::assertNotNull($exception->getPrevious());
        } finally {
            unlink($path);
        }
    }
}
