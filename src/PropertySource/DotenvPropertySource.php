<?php

declare(strict_types=1);

namespace SuperKernel\Environment\PropertySource;

use SuperKernel\Environment\Contract\PropertySourceInterface;
use SuperKernel\Environment\Exception\PropertySourceLoadingException;
use SuperKernel\Environment\Naming\PropertyName;
use Symfony\Component\Dotenv\Dotenv;
use Throwable;

use function array_key_exists;
use function file_get_contents;
use function is_file;
use function is_string;
use function sprintf;

final readonly class DotenvPropertySource implements PropertySourceInterface
{
    /** @var array<string, string> */
    private array $values;

    public function __construct(private string $path, bool $optional = true)
    {
        if (!is_file($path)) {
            if ($optional) {
                $this->values = [];

                return;
            }

            throw new PropertySourceLoadingException(sprintf(
                'Required dotenv file %s does not exist.',
                $path,
            ));
        }

        $contents = @file_get_contents($path);

        if (!is_string($contents)) {
            throw new PropertySourceLoadingException(sprintf(
                'Unable to read dotenv file %s.',
                $path,
            ));
        }

        try {
            $this->values = new Dotenv()->parse($contents, $path);
        } catch (Throwable $throwable) {
            throw new PropertySourceLoadingException(sprintf(
                'Unable to parse dotenv file %s.',
                $path,
            ), 0, $throwable);
        }
    }

    public function name(): string
    {
        return 'dotenv:' . $this->path;
    }

    public function has(string $name): bool
    {
        return array_key_exists(PropertyName::environmentVariable($name), $this->values);
    }

    public function get(string $name): ?string
    {
        $value = $this->values[PropertyName::environmentVariable($name)] ?? null;

        return is_string($value) ? $value : null;
    }
}
