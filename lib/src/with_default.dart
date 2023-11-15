import 'package:ovo/src/parser.dart';

import 'nullable.dart';
import 'schema.dart';
import 'transform.dart';

extension OvoWithDefault<T> on OvoSchema<T> {
  OvoSchema<T> withDefault(T defaultValue) {
    return nullable().transform<T>((context, data) {
      return switch (data) {
        T value => context.ok(value),
        null => context.ok(defaultValue),
      };
    });
  }
}
