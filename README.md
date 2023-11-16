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

I am very grateful and encouraged for any level of sponsorship, which will help me continue to develop and maintain this project.

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

OvO provides type validation with dependent type parameters, and also built-in some common types. You can declare a type validation by `OvO<T>`, where `T` is a type parameter and can be any type.

Let's try to create a `String` type validation:

```dart
import 'package:ovo/ovo.dart' as ovo;

final schema = ovo.OvO<String>();

await schema.parse('Hello World'); // => 'Hello World'
await schema.parse(123); // => throws OvOException
```

Or, we create a validation of `Record` type that is not built-in:

```dart
import 'package:ovo/ovo.dart' as ovo;

final schema = ovo.OvO<(int, String)>();

await schema.parse((123, 'Hello World')); // => (123, 'Hello World')
```

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

`Any` type validation can accept a non-`null` value of any type. It is an alias of `OvO<Object>`.

### Array

`Array` type validation can accept an iterable value (for example: `List`, `Set`, `Iterable`, etc.). It is not an alias of `OvO<Iterable<T>>`, but a specific type validation.

`Array` accepts a parameter of type `OvO<T>` to validate each element in the array.

```dart
import 'package:ovo/ovo.dart' as ovo;

final schema = ovo.Array(ovo.String());

await schema.parse(['Hello', 'World']); // => ['Hello', 'World']
await schema.parse([123, 456]); // => throws OvOException
```

#### `.min`/`.max`/`.size`

`.min`/`.max`/`.size` have the same parameters and type signatures, and they all accept a parameter of type `int` to validate the length of the array.

- `.min` validates that the length of the array must be greater than or equal to the specified length.
- `.max` validates that the length of the array must be less than or equal to the specified length.
- `.size` validates that the length of the array must be equal to the specified length.

```dart
ovo.Array(ovo.String()).min(2); // must contain at least 2 elements
ovo.Array(ovo.String()).max(2); // must contain no more than 2 elements
ovo.Array(ovo.String()).size(2); // must contain exactly 2 elements
```

#### `.unique`

`.unique` validates that the elements in the array must be unique, similar to `Set` in Dart.

```dart
ovo.Array(ovo.String()).unique(); // must contain unique elements
```

### Boolean

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

Of course, you can use `OvO<bool>` instead of `Boolean` type validation, but `Boolean` type validation is more semantic.

### Number

`Number` type validation can accept a value of type `num`. It is an alias of `OvO<num>`.

```dart
ovo.Number(); // must be a number, `int` or `double`
```

If you need to validate an integer, you can use the `Integer` type validation, which is an alias of `OvO<int>`. To validate a floating-point number, you can use the `Double` type validation, which is an alias of `OvO<double>`.

`Integer` and `Double` are both subtypes of `Number`:

```dart
ovo.Integer(); // must be an integer, `int`
ovo.Double(); // must be a double, `double`
```

`OvO<num>` also contains some additional methods:

```dart
ovo.Number().gt(10); // must be greater than 10
ovo.Number().gte(10); // must be greater than or equal to 10
ovo.Number().lt(10); // must be less than 10
ovo.Number().lte(10); // must be less than or equal to 10
ovo.Number().finite(); // must be finite
ovo.Number().negative(); // must be negative
```

### String

`String` type validation can accept a value of type `String`. It is an alias of `OvO<String>`.

```dart
ovo.String(); // must be a string, `String`
```

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

The `.nullable` method can convert a type validation to a type validation that accepts `null`.

```dart
ovo.String().nullable(); // must be a string or null
```

### `.refine`

`.refine` is a method that allows you to customize the validation. It accepts a validation function of `FutureOr<bool> Function(T data)` to facilitate validation according to the actual situation.

```dart
ovo.String().refine(
    (value) => value.length > 5,
    message: 'must be greater than 5',
); // must be a string and length greater than 5
```

> It is worth noting that many of the built-in extension methods are implemented based on the `.refine` method.

### `.transform`

`.transform` is a method that allows you to customize the method of converting data types. It works on the principle of **Onion Model**.

#### Pre-transformation

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

#### Post-transformation

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

`.withDefault` allows a nullable value `T?` to be replaced with a default value `T` when the value is `null`.

```dart
final schema = ovo.String().withDefault('Hello World'); // must be a string or null, default value is 'Hello World'

await schema.parse(null); // => 'Hello World'
await schema.parse('Hello'); // => 'Hello'
```

As you can see, the `.withDefault` method will automatically attach the `.nullable` method, so you don't need to call the `.nullable` method manually.

## Compositions

### `AnyOf` (OR)

`AnyOf` type validation can accept any of the multiple type validations.

```dart
ovo.AnyOf([
    ovo.String(),
    ovo.Integer(),
]); // must be a string or an integer
```

### `AllOf` (AND)

`AllOf` type validation can accept all types in multiple type validations.

```dart
ovo.AllOf([
    ovo.Double(),
    ovo.Integer(),
]); // must be a double and an integer
```

### `OneOf` (XOR)

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

`Not` type validation can accept any type validation, but if the specified type validation is matched, an exception is thrown.

```dart
final schema = ovo.Not(ovo.String());

await schema.parse(123); // => 123
await schema.parse({'name': 'Seven'}) // => {'name': 'Seven'}
await schema.parse('123'); // => throws OvOException
```

### `Const`

`Const` type validation can accept a constant value.

```dart
ovo.Const(123); // must be 123
```

Using `Const` we can implement string literals in JSON:

```dart
final schema = ovo.OneOf([
    ovo.Const('mobile'),
    ovo.Const('web'),
]);

await schema.parse('mobile'); // => 'mobile'
await schema.parse('web'); // => 'web'
```

Used in conjunction with conversion, we can implement `Enum` type validation:

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
