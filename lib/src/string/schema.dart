import '../schema.dart';
import '../refine.dart';

extension OvoStringSchema on OvoSchema<String> {
  OvoSchema<String> max(int length, {String? message}) {
    return refine(
      (value) => value.length <= length,
      message: message ?? 'String must be at most $length characters long',
    );
  }

  OvoSchema<String> min(int length, {String? message}) {
    return refine(
      (value) => value.length >= length,
      message: message ?? 'String must be at least $length characters long',
    );
  }

  OvoSchema<String> length(int length, {String? message}) {
    return refine(
      (value) => value.length == length,
      message: message ?? 'String must be exactly $length characters long',
    );
  }

  OvoSchema<String> pattern(RegExp pattern, {String? message}) {
    return refine(
      (value) => pattern.hasMatch(value),
      message: message ?? 'String must match pattern $pattern',
    );
  }

  OvoSchema<String> contains(String value, {String? message}) {
    return refine(
      (data) => data.contains(value),
      message: message ?? 'String must contain $value',
    );
  }

  OvoSchema<String> startsWith(String value, {String? message}) {
    return refine(
      (data) => data.startsWith(value),
      message: message ?? 'String must start with $value',
    );
  }

  OvoSchema<String> endsWith(String value, {String? message}) {
    return refine(
      (data) => data.endsWith(value),
      message: message ?? 'String must end with $value',
    );
  }

  OvoSchema<String> equals(
    String value, {
    String? message,
    bool caseSensitive = true,
  }) {
    return refine(message: message ?? 'String must equal $value', (value) {
      return switch (caseSensitive) {
        true => value == value,
        false => value.toLowerCase() == value.toLowerCase(),
      };
    });
  }
}
