<?php

declare(strict_types=1);

namespace SuperKernel\Environment\Exception;

use InvalidArgumentException;

final class InvalidPropertySourceException extends InvalidArgumentException implements EnvironmentExceptionInterface
{
}
