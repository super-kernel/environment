<p align="right">
  <strong>English</strong> | <a href="README.zh-CN.md">中文文档</a>
</p>

# SuperKernel Environment

`super-kernel/environment` exposes ordered external property sources through a strict, typed object API. It owns source precedence and scalar conversion, but it does not define defaults or validation policy for Framework, Server, database, cache, or other consuming components.

## Property sources

`Environment` accepts a list of `PropertySourceInterface` implementations. Sources are ordered from lowest to highest precedence, so a value in a later source overrides the same property from every earlier source. The package includes array, optional or required dotenv-file, and process-environment sources.

Canonical property names use lowercase dotted or kebab notation, for example:

```text
app.feature.enabled
```

Process and dotenv sources map that name to:

```text
APP_FEATURE_ENABLED
```

A dotenv source reads one immutable snapshot without mutating `$_ENV`, `$_SERVER`, or the process environment. A missing optional file contributes no values; a missing required file raises `PropertySourceLoadingException`.

## Typed access

`EnvironmentInterface` exposes raw scalar lookup plus string, integer, float, and boolean conversion. Missing values return `null`. Invalid explicit values raise `InvalidEnvironmentValueException` without including the configured value in the exception message. Component configuration factories apply their own defaults only after a missing lookup and validate their domain-specific invariants.

The default `EnvironmentProvider` installs only the process environment. An application may provide its own `EnvironmentInterface` implementation to add a project-root dotenv file or another source before the process source.
