<p align="center">
    <h1 align="center">OvO</h1>
    <p align="center">
        <!-- ✨ <a href="https://zod.dev">https://zod.dev</a> ✨ -->
    </p>
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

## Installation

> We are more aggressive and use higher versions of Dart stable versions as much as possible.

### Install from command line

```bash
dart pub add ovo
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
final schema = ovo.string();

// Parsing
await schema.parse('Hello World'); // => 'Hello World'
await schema.parse(123); // => throws OvoError

// Safe parsing, don't throw error.
await schema.safeParse('Hello World'); // => OvoSuccess<String>('Hello World')
await schema.safeParse(123); // => OvoFailure<String>('Expected a string, but received a int')
```

Creating an JSON schema:

```dart
import 'package:ovo/ovo.dart' as ovo;

final schema = ovo.json({
    'name': ovo.string(),
});

await schema.parse({
    'name': 'John',
}); // => {'name': 'John'}
```
