<p align="right">
  <strong>English</strong> | <a href="README.zh-CN.md">中文文档</a>
</p>

# SuperKernel Environment

`super-kernel/environment` reads external properties, such as environment variables and `.env` files, from ordered sources and returns them as strictly checked strings, integers, floats, and booleans.

## Installation

```bash
composer require super-kernel/environment
```

The package needs the `filter` extension, which PHP includes by default, and installs `symfony/dotenv` to parse `.env` files.

## What it provides

- An environment that reads properties from a list of sources, where a later source overrides an earlier one.
- Sources for arrays, `.env` files, and the process environment.
- Typed accessors that return `null` for a missing property and reject an explicit value of the wrong kind.
- One property name, such as `mail.port`, for every source: process and `.env` sources read it as `MAIL_PORT`.
- A container entry for `EnvironmentInterface` that reads the process environment.

It does not:

- define defaults or check values for other components; your configuration sources do;
- change `$_ENV`, `$_SERVER`, or the process environment when it reads a `.env` file;
- offer profiles, remote configuration, or reloading.

## Quick start

Read typed values with defaults below the process environment. Save the script as `bin/environment.php`:

```php
<?php

declare(strict_types=1);

use SuperKernel\Environment\Environment;
use SuperKernel\Environment\PropertySource\ArrayPropertySource;
use SuperKernel\Environment\PropertySource\ProcessEnvironmentPropertySource;

require dirname(__DIR__) . '/vendor/autoload.php';

$environment = new Environment([
    new ArrayPropertySource('defaults', ['app.debug' => 'off', 'app.workers' => '4']),
    new ProcessEnvironmentPropertySource(),
]);

var_dump($environment->getBool('app.debug'));
var_dump($environment->getInt('app.workers'));
var_dump($environment->getString('app.name'));
```

Run the script with a process variable:

```bash
APP_DEBUG=yes php bin/environment.php
```

It prints:

```text
bool(true)
int(4)
NULL
```

## Public surface

| Name | Kind | Purpose |
| --- | --- | --- |
| `SuperKernel\Environment\Contract\EnvironmentInterface` | Interface | `has()`, raw `get()`, and typed `getString()`, `getInt()`, `getFloat()`, and `getBool()`. |
| `SuperKernel\Environment\Environment` | Class | The environment over a list of sources, ordered from lowest to highest precedence. |
| `SuperKernel\Environment\Contract\PropertySourceInterface` | Interface | Implemented by a source: its name, whether it has a property, and the raw value. |
| `SuperKernel\Environment\PropertySource\ArrayPropertySource` | Class | Properties from an array, keyed by property name. |
| `SuperKernel\Environment\PropertySource\DotenvPropertySource` | Class | Properties from one `.env` file, optional by default. |
| `SuperKernel\Environment\PropertySource\ProcessEnvironmentPropertySource` | Class | Properties from the process environment. |
| `SuperKernel\Environment\Provider\EnvironmentProvider` | Class | The factory provider of `EnvironmentInterface`, reading the process environment only. |

## Behavior to know

- Property names are lowercase, with dots or hyphens, such as `mail.port` or `app.feature-flag`. Any other name raises `SuperKernel\Environment\Exception\InvalidPropertyNameException` with the message `Property names must use non-empty lowercase dotted or kebab notation.`
- A typed accessor raises `SuperKernel\Environment\Exception\InvalidEnvironmentValueException` for a value of the wrong kind, such as `Environment property app.workers must be an integer; string provided.` The message never contains the value.
- Booleans accept `1`, `0`, `true`, `false`, `yes`, `no`, `on`, and `off`, in any case.
- A required `.env` file that is missing, unreadable, or malformed raises `SuperKernel\Environment\Exception\PropertySourceLoadingException`; a missing optional file contributes nothing.
- The container's default environment does not read `.env` files; an application adds one by providing its own `EnvironmentInterface`.

## Documentation

The developer documentation describes the sources, precedence, conversions, `.env` files, and use in an application with complete examples: [SuperKernel Environment](https://github.com/super-kernel/.github/blob/main/docs/components/environment.md).

## License

Released under the MIT License. See [LICENSE](LICENSE).
