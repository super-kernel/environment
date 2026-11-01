<p align="right">
  <a href="README.md">English</a> | <strong>中文文档</strong>
</p>

# SuperKernel Environment

`super-kernel/environment` 通过严格类型的对象 API 提供有序的外部属性源。它负责属性源的优先级和标量转换，但不为 Framework、Server、数据库、缓存或其他使用方组件定义默认值或校验策略。

## 属性源

`Environment` 接收一个 `PropertySourceInterface` 实现的列表。属性源按优先级从低到高排列，后面的源中的值会覆盖前面所有源中的同名属性。本包内置数组源、可选或必需的 dotenv 文件源，以及进程环境变量源。

规范的属性名使用小写的点分或短横线写法，例如：

```text
app.feature.enabled
```

进程环境变量源和 dotenv 源会把这个名称映射为：

```text
APP_FEATURE_ENABLED
```

dotenv 源只读取一份不可变的快照，不会修改 `$_ENV`、`$_SERVER` 或进程环境变量。缺失的可选文件不提供任何值；缺失的必需文件会抛出 `PropertySourceLoadingException`。

## 类型化访问

`EnvironmentInterface` 提供原始标量查询，以及字符串、整数、浮点数和布尔值转换。缺失的值返回 `null`。显式配置但无效的值会抛出 `InvalidEnvironmentValueException`，异常消息中不包含所配置的值。各组件的配置工厂只在查询结果缺失时才应用自己的默认值，并自行校验各自领域的约束。

默认的 `EnvironmentProvider` 只安装进程环境变量源。应用可以提供自己的 `EnvironmentInterface` 实现，在进程环境变量源之前加入项目根目录的 dotenv 文件或其他属性源。
