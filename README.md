<p align="center">
    <h1 align="center">✨ OvO ✨</h1>
    <p align="center">
        OvO is a Dart-first schema declaration and validation library.
    </p>
</p>

## Introduction

OvO is a Dart-first schema declaration and validation library. We use the technical term **"Schema"** to define any data type, from simple single data (for example: `string`/`int`, etc.) to complex nested `Map`.

OvO is designed to be as user-friendly and developer-friendly as possible, with the goal of eliminating tedious type checking and object deserialization. It is easy to compose complex data structure validation using simple declaration validation.

several important aspects

- A fun walkthrough of Dart type extensions
- Simple and chained interface calls
- Can be used on any Dart platform (Dart, Web, Flutter)

### Sponsors

我非常感谢和鼓励任何级别的赞助，这将有助于我继续开发和维护这个项目。

- [GitHub Sponsors](https://github.com/sponsors/odroe)
- [Open collective](https://opencollective.com/openodroe)
- [Patreon](https://www.patreon.com/user?u=80114587)

## Installation

> We are more aggressive and use higher versions of Dart stable versions as much as possible.

### Install from command line

```bash
# Dart project
dart pub add ovo

# Flutter project
flutter pub add ovo
```

### Install from `pubspec.yaml`

```yaml
dependencies:
  ovo: latest
```

## Basic Usage

Create a simple string schema:

```dart
import 'package:ovo/ovo.dart' as ovo;

// Create a schema for string.
final schema = ovo.String();

// Parsing
await schema.parse('Hello World'); // => 'Hello World'
await schema.parse(123); // => throws OvOException
```

Creating an JSON schema:

```dart
import 'package:ovo/ovo.dart' as ovo;

final schema = ovo.Object({
    'name': ovo.String(),
});

await schema.parse({
    'name': 'John',
}); // => {'name': 'John'}
```

## Types

OvO 提供了依赖类型参数的类型验证，也内置了一些常用的类型。你可以通过 `OvO<T>` 来声明一个类型验证，其中 `T` 为类型参数，可以是任意类型。

OvO provides type validation with dependent type parameters, and also built-in some common types. You can declare a type validation by `OvO<T>`, where `T` is a type parameter and can be any type.

我们现在试一下创建一个 `String` 类型的验证：

Let's try to create a `String` type validation:

```dart
import 'package:ovo/ovo.dart' as ovo;

final schema = ovo.OvO<String>();

await schema.parse('Hello World'); // => 'Hello World'
await schema.parse(123); // => throws OvOException
```

或者，我们创建一个没有内置的 `Record` 类型的验证：

Or, we create a validation of `Record` type that is not built-in:

```dart
import 'package:ovo/ovo.dart' as ovo;

final schema = ovo.OvO<(int, String)>();

await schema.parse((123, 'Hello World')); // => (123, 'Hello World')
```

当然，你也可以使用它来验证一个自定义的类：

Of course, you can also use it to validate a custom class:

```dart
import 'package:ovo/ovo.dart' as ovo;

class User {
    final String name;
    final int age;

    User(this.name, this.age);
}

final schema = ovo.OvO<User>();

await schema.parse(User('John', 18)); // => User('John', 18)
```

基础的类型验证依赖 Dart 内置的 `is` 关键词，完全可以胜任大部分的类型验证。但是，如果你需要更复杂的类型验证，你可以使用 `OvO<T>` 的构造函数来创建一个自定义的类型验证。

Basic type validation depends on the built-in `is` keyword in Dart, which is fully capable of most type validation. However, if you need more complex type validation, you can use the constructor of `OvO<T>` to create a custom type validation.

```dart
import 'package:ovo/ovo.dart' as ovo;

class User {
    final String name;
    final int age;

    User(this.name, this.age);

    factory User.fromJson(Map<String, dynamic> json) {
        return User(json['name'], json['age']);
    }
}

class MyOvO implements ovo.OvO<User> {
    @override
    Future<User> parse(ovo.Context, dynamic value) {
        if (value is Map<String, dynamic>) {
            return User.fromJson(value);
        }
        throw ovo.OvOException('Invalid value');
    }
}

final schema = ovo.Object({
    'data': MyOvO(),
    'status': ovo.Boolean(),
});

final data = {
    'data': {
        'name': 'John',
        'age': 18,
    },
    'status': true,
};

await schema.parse(data); // => {'data': User('John', 18), 'status': true}
```

### Any

`Any` 类型验证可以接受一个非 `null` 的任意类型的值。它是 `OvO<Object>` 的别名。

`Any` type validation can accept a non-`null` value of any type. It is an alias of `OvO<Object>`.

### Array

`Array` 类型验证可以接受一个可迭代的值（例如：`List`、`Set`、`Iterable` 等）。它不是 `OvO<Iterable<T>>` 的别名，而是一种具体的类型验证。

`Array` type validation can accept an iterable value (for example: `List`, `Set`, `Iterable`, etc.). It is not an alias of `OvO<Iterable<T>>`, but a specific type validation.

`Array` 接收一个 `OvO<T>` 类型的参数，用于验证数组中的每一个元素。

`Array` accepts a parameter of type `OvO<T>` to validate each element in the array.

```dart
import 'package:ovo/ovo.dart' as ovo;

final schema = ovo.Array(ovo.String());

await schema.parse(['Hello', 'World']); // => ['Hello', 'World']
await schema.parse([123, 456]); // => throws OvOException
```

#### `.min`/`.max`/`.size`

`.min`/`.max`/`.size` 具有相同的参数和类型签名，它们都接收一个 `int` 类型的参数，用于验证数组的长度。

`.min`/`.max`/`.size` have the same parameters and type signatures, and they all accept a parameter of type `int` to validate the length of the array.

- `.min` 验证数组的长度必须大于等于指定的长度。
- `.max` 验证数组的长度必须小于等于指定的长度。
- `.size` 验证数组的长度必须等于指定的长度。

- `.min` validates that the length of the array must be greater than or equal to the specified length.
- `.max` validates that the length of the array must be less than or equal to the specified length.
- `.size` validates that the length of the array must be equal to the specified length.

```dart
ovo.Array(ovo.String()).min(2); // must contain at least 2 elements
ovo.Array(ovo.String()).max(2); // must contain no more than 2 elements
ovo.Array(ovo.String()).size(2); // must contain exactly 2 elements
```

#### `.unique`

`.unique` 验证数组中的元素必须是唯一的，这与 Dart 中的 `Set` 类似。

`.unique` validates that the elements in the array must be unique, similar to `Set` in Dart.

```dart
ovo.Array(ovo.String()).unique(); // must contain unique elements
```

### Boolean

`Boolean` 类型验证可以接受一个 `bool` 类型的值。它是 `OvO<bool>` 的别名。

`Boolean` type validation can accept a value of type `bool`. It is an alias of `OvO<bool>`.

另外，他还有两个额外的扩展方法：

In addition, it has two additional extension methods:

- `.isTrue` - 验证值必须为 `true`。
- `.isFalse` - 验证值必须为 `false`。

- `.isTrue` - validates that the value must be `true`.
- `.isFalse` - validates that the value must be `false`.

```dart
ovo.Boolean(); // must be a boolean, `true` or `false`
ovo.Boolean().isTrue(); // must be true
ovo.Boolean().isFalse(); // must be false
```

当然，你可以使用 `OvO<bool>` 来替代 `Boolean` 类型验证，但是 `Boolean` 类型验证更加语义化。

Of course, you can use `OvO<bool>` instead of `Boolean` type validation, but `Boolean` type validation is more semantic.

### Number

`Number` 类型验证可以接受一个 `num` 类型的值。它是 `OvO<num>` 的别名。

`Number` type validation can accept a value of type `num`. It is an alias of `OvO<num>`.

```dart
ovo.Number(); // must be a number, `int` or `double`
```

如果你需要验证一个整数，你可以使用 `Integer` 类型验证，它是 `OvO<int>` 的别名。验证一个浮点数，你可以使用 `Double` 类型验证，它是 `OvO<double>` 的别名。

If you need to validate an integer, you can use the `Integer` type validation, which is an alias of `OvO<int>`. To validate a floating-point number, you can use the `Double` type validation, which is an alias of `OvO<double>`.

`Integer` 和 `Double` 都是 `Number` 的子类型:

`Integer` and `Double` are both subtypes of `Number`:

```dart
ovo.Integer(); // must be an integer, `int`
ovo.Double(); // must be a double, `double`
```

`OvO<num>` 中还包含了一些额外的方法：

```dart
ovo.Number().gt(10); // must be greater than 10
ovo.Number().gte(10); // must be greater than or equal to 10
ovo.Number().lt(10); // must be less than 10
ovo.Number().lte(10); // must be less than or equal to 10
ovo.Number().finite(); // must be finite
ovo.Number().negative(); // must be negative
```

### String

`String` 类型验证可以接受一个 `String` 类型的值。它是 `OvO<String>` 的别名。

`String` type validation can accept a value of type `String`. It is an alias of `OvO<String>`.

```dart
ovo.String(); // must be a string, `String`
```

`OvO<String>` 中还包含了一些额外的方法：

`OvO<String>` also contains some additional methods:

```dart
// Validations
ovo.String().min(5); // must be at least 5 characters long
ovo.String().max(5); // must be no more than 5 characters long
ovo.String().length(5); // must be exactly 5 characters long
ovo.String().regex(RegExp(r'^[a-z]+$')); // must match the regular expression
ovo.String().contains('abc'); // must contain the substring
ovo.String().isNotEmpty(); // must not be empty
ovo.String().startsWith('abc'); // must start with the substring
ovo.String().endsWith('abc'); // must end with the substring
ovo.String().equals('abc'); // must be equal to the string

// Transformations
ovo.String().trim(); // trim whitespace
ovo.String().toLowerCase(); // convert to lowercase
ovo.String().toUpperCase(); // convert to uppercase
```

### Object

`Object` 类型验证可以接受一个 `Map` 类型的值。它是 `OvO<Map<String, T>>` 的实现。

`Object` type validation can accept a value of type `Map`. It is an implementation of `OvO<Map<String, T>>`.

```dart
import 'package:ovo/ovo.dart' as ovo;

final user = ovo.Object({
    'name': ovo.String(),
    'age': ovo.Number(),
});

await user.parse({
    'name': 'John',
    'age': 18,
}); // => {'name': 'John', 'age': 18}

await user.parse({
    'name': 'John',
    'age': '18',
}); // => throws OvOException
```

当然，如果你只是想简单验证一个 `Map<K, T>` 类型的值，你可以使用 `OvO<Map<K, T>>` 来代替 `Object` 类型验证。

Of course, if you just want to simply validate a value of type `Map<K, T>`, you can use `OvO<Map<K, T>>` instead of `Object` type validation.

```dart
import 'package:ovo/ovo.dart' as ovo;

final user = ovo.OvO<Map<String, dynamic>>();

await user.parse({
    'name': 'John',
    'age': 18,
}); // => {'name': 'John', 'age': 18}

await user.parse({
    'name': 'John',
    'age': '18',
}); // => {'name': 'John', 'age': '18'}
```

## Functional

### `.nullable`

`.nullable` 方法可以将一个类型验证转换为可接受 `null` 的类型验证。

The `.nullable` method can convert a type validation to a type validation that accepts `null`.

```dart
ovo.String().nullable(); // must be a string or null
```

### `.refine`

`.refine` 是一个允许你自定义验证的方法，它接受一个 `FutureOr<bool> Function(T data)` 的验证函数，以方便根据实际情况进行验证。

`.refine` is a method that allows you to customize the validation. It accepts a validation function of `FutureOr<bool> Function(T data)` to facilitate validation according to the actual situation.

```dart
ovo.String().refine(
    (value) => value.length > 5,
    message: 'must be greater than 5',
); // must be a string and length greater than 5
```

> 值得注意的是，内置的许多扩展方法都是基于 `.refine` 方法实现的。

### `.transform`

`.transform` 是一个允许你自定义转换数据类型的方法。它的工作原理是**洋葱模型**。

`.transform` is a method that allows you to customize the method of converting data types. It works on the principle of **Onion Model**.

#### 前置转换

#### Pre-transformation

前置转换允许你预先处理等待解析的原始数据，然后再将其转交给下一个转换器，最后再交给类型验证器进行验证：

Pre-transformation allows you to pre-process the raw data to be parsed, and then hand it over to the next converter, and finally hand it over to the type validator for verification:

```dart

final schema = ovo.String().transform(
    (ovo.Context context, dynamic data, Future<T> Function(dynamic data) next) {
        // data convert to string
        return next(data.toString());
    }
);

await schema.parse(123); // => '123'
await schema.parse(#symbol); // => '#symbol'
```

#### 后置转换

#### Post-transformation

利用回调中的 `next` 参数，你可以在类型验证器验证成功后，对数据进行后置转换：

Using the `next` parameter in the callback, you can perform post-transformation of the data after the type validator is verified successfully:

```dart
final schema = ovo.String().transform(
    (ovo.Context context, dynamic data, Future<T> Function(dynamic data) next) async {
        final value = await next(data);

        // value convert to int
        return int.parse(value);
    }
);

await schema.parse('123'); // => 123
```

### `.withDefault`

`.withDefault` 允许一个可空类型的值 `T?` 当值为 `null` 时，使用默认值 `T` 来替代。

`.withDefault` allows a nullable value `T?` to be replaced with a default value `T` when the value is `null`.

```dart
final schema = ovo.String().withDefault('Hello World'); // must be a string or null, default value is 'Hello World'

await schema.parse(null); // => 'Hello World'
await schema.parse('Hello'); // => 'Hello'
```

如你所见，`.withDefault` 方法会自动附加 `.nullable` 方法，因此你不需要手动调用 `.nullable` 方法。

## Compositions

### `AnyOf` (OR)

`AnyOf` 类型验证可以接受多个类型验证中的任意一个。

`AnyOf` type validation can accept any of the multiple type validations.

```dart
ovo.AnyOf([
    ovo.String(),
    ovo.Integer(),
]); // must be a string or an integer
```

### `AllOf` (AND)

`AllOf` 类型验证可以接受多个类型验证中的所有类型。

`AllOf` type validation can accept all types in multiple type validations.

```dart
ovo.AllOf([
    ovo.Double(),
    ovo.Integer(),
]); // must be a double and an integer
```

### `OneOf` (XOR)

`OneOf` 类型验证可以接受多个类型验证中的一个类型，如果多个验证都匹配，则抛出异常。

`OneOf` type validation can accept one type in multiple type validations. If multiple validations match, an exception is thrown.

```dart
final schema = ovo.OneOf([
    ovo.String().size(5),
    ovo.String().size(10),
]);

await schema.parse('12345'); // => '12345'
await schema.parse('1234567890'); // => '1234567890'
await schema.parse('123456'); // => throws OvOException
```

### `Not` (NOT)

`Not` 类型验证可以接受任意一个类型验证，但是如果匹配了指定的类型验证，则抛出异常。

`Not` type validation can accept any type validation, but if the specified type validation is matched, an exception is thrown.

```dart
final schema = ovo.Not(ovo.String());

await schema.parse(123); // => 123
await schema.parse({'name': 'Seven'}) // => {'name': 'Seven'}
await schema.parse('123'); // => throws OvOException
```

### `Const`

`Const` 类型验证可以接受一个常量值。

`Const` type validation can accept a constant value.

```dart
ovo.Const(123); // must be 123
```

利用 `Const` 我们可以实现 JSON 中的字符串字面量：

Using `Const` we can implement string literals in JSON:

```dart
final schema = ovo.OneOf([
    ovo.Const('mobile'),
    ovo.Const('web'),
]);

await schema.parse('mobile'); // => 'mobile'
await schema.parse('web'); // => 'web'
```

与转换配合使用，我们可以实现 `Enum` 类型验证：

```dart
enum MyEnum {
    mobile,
    web,
}

final schema = ovo.OneOf(
    MyEnum.values.map((e) => ovo.Const(e.name)),
).transform<MyEnum>(
    (ovo.Context context, dynamic data, Future<T> Function(dynamic data) next) async {
        final value = await next(data);

        return MyEnum.values.firstWhere((e) => e.name == value);
    }
);

await schema.parse('mobile'); // => MyEnum.mobile
await schema.parse('web'); // => MyEnum.web
await schema.parse('desktop'); // => throws OvOException
```
