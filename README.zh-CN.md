<p align="right">
  <a href="README.md">English</a> | <strong>中文文档</strong>
</p>

# SuperKernel Environment

`super-kernel/environment` 从有序的来源中读取外部属性（例如环境变量和 `.env` 文件），并以经过严格检查的字符串、整数、浮点数和布尔值返回它们。

## 安装

```bash
composer require super-kernel/environment
```

本包需要 PHP 默认自带的 `filter` 扩展，并会安装用于解析 `.env` 文件的 `symfony/dotenv`。

## 提供的能力

- 一个从来源列表中读取属性的环境，后面的来源覆盖前面的来源。
- 数组、`.env` 文件和进程环境这三种来源。
- 类型化的读取方法：属性缺失时返回 `null`，显式给出但类型不对的值会被拒绝。
- 所有来源使用同一个属性名称，例如 `mail.port`：进程和 `.env` 来源把它读作 `MAIL_PORT`。
- 一个读取进程环境的 `EnvironmentInterface` 容器条目。

它不会：

- 为其他组件定义默认值或检查值，这由你的配置来源负责；
- 在读取 `.env` 文件时修改 `$_ENV`、`$_SERVER` 或进程环境；
- 提供 profile、远程配置或重新加载。

## 快速开始

在进程环境之下提供默认值，并读取类型化的值。将脚本保存为 `bin/environment.php`：

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

带一个进程变量运行脚本：

```bash
APP_DEBUG=yes php bin/environment.php
```

它会打印：

```text
bool(true)
int(4)
NULL
```

## 公开接口

| 名称 | 类别 | 用途 |
| --- | --- | --- |
| `SuperKernel\Environment\Contract\EnvironmentInterface` | 接口 | `has()`、原始值 `get()`，以及类型化的 `getString()`、`getInt()`、`getFloat()` 和 `getBool()`。 |
| `SuperKernel\Environment\Environment` | 类 | 基于来源列表的环境，来源按优先级从低到高排列。 |
| `SuperKernel\Environment\Contract\PropertySourceInterface` | 接口 | 由来源实现：它的名称、是否有某个属性，以及原始值。 |
| `SuperKernel\Environment\PropertySource\ArrayPropertySource` | 类 | 来自数组的属性，以属性名称为键。 |
| `SuperKernel\Environment\PropertySource\DotenvPropertySource` | 类 | 来自一个 `.env` 文件的属性，默认是可选的。 |
| `SuperKernel\Environment\PropertySource\ProcessEnvironmentPropertySource` | 类 | 来自进程环境的属性。 |
| `SuperKernel\Environment\Provider\EnvironmentProvider` | 类 | `EnvironmentInterface` 的工厂提供者，只读取进程环境。 |

## 需要了解的行为

- 属性名称为小写，使用点或连字符，例如 `mail.port` 或 `app.feature-flag`。其他任何名称都会抛出 `SuperKernel\Environment\Exception\InvalidPropertyNameException`，消息为 `Property names must use non-empty lowercase dotted or kebab notation.`。
- 对于类型不对的值，类型化读取方法抛出 `SuperKernel\Environment\Exception\InvalidEnvironmentValueException`，例如 `Environment property app.workers must be an integer; string provided.`。消息中从不包含值本身。
- 布尔值接受 `1`、`0`、`true`、`false`、`yes`、`no`、`on` 和 `off`，不区分大小写。
- 必需的 `.env` 文件缺失、不可读或格式错误时，抛出 `SuperKernel\Environment\Exception\PropertySourceLoadingException`；缺失的可选文件不提供任何值。
- 容器的默认环境不读取 `.env` 文件；应用通过提供自己的 `EnvironmentInterface` 来加入它。

## 文档

开发者文档用完整示例介绍各种来源、优先级、类型转换、`.env` 文件以及在应用中的用法：[SuperKernel Environment](https://github.com/super-kernel/.github/blob/main/docs/components/environment.zh-CN.md)。

## 许可证

基于 MIT 许可证发布，详见 [LICENSE](LICENSE)。
