<?php

declare(strict_types=1);

namespace SuperKernel\Environment\Exception;

use RuntimeException;

final class InvalidEnvironmentValueException extends RuntimeException implements EnvironmentExceptionInterface
{
}
